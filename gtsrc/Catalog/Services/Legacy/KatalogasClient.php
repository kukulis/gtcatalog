<?php
namespace Gt\Catalog\Services\Legacy;

use Catalog\B2b\Common\Data\Legacy\Catalog\KatalogasPreke;
use Catalog\B2b\Common\Data\Legacy\Mock\PrekesRestResult;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Log\LoggerInterface;
use \Exception;

// TODO move to a separate repository
/**
 * Class PrekesService katalogo rest klientas.
 * @package Sketis\PrekesBundle\Service\Spec
 */
class KatalogasClient
{
    const CLIENT_CODE_PLACEHOLDER = 'CLIENT_CODE';
    const MAX_UZKLAUSU_KATALOGUI = 3;

    const SEARCH_PREKES_URI = '/prekes';

    const ERROR_SIZE = 4000;

    /** @var \GuzzleHttp\Client $guzzleClient */
    private $guzzleClient;

    /** @var string */
    private $headerAccept;

    /** @var string */
    private $katalogasRestBaseUrl;

    /** @var string */
    private $katalogasSiteBaseUrl;

    /**
     * PrekesRestService constructor.
     * @param Client $guzzleClient
     * @param string $headerAccept
     * @param LoggerInterface $logger
     */
    public function __construct(Client $guzzleClient, $headerAccept, LoggerInterface $logger)
    {
        $this->guzzleClient = $guzzleClient;
        $this->headerAccept = $headerAccept;
        $this->logger = $logger;
    }

    /**
     * @param string[] $nomnrs
     * @param string $locale
     * @return KatalogasPreke[]
     */
    private function getPrekesOnlyInner($nomnrs, $locale)
    {
        $url = $this->katalogasRestBaseUrl . self::SEARCH_PREKES_URI . '/' . $locale;
        $nomNrsJson = json_encode($nomnrs);
        $postData = [
            'headers' => ['Accept' => $this->headerAccept],
            'body' => $nomNrsJson
        ];
        try {
            $this->logger->debug('Kreipiamės į '.$url.' su duomenimis '.$nomNrsJson );
            $res = $this->guzzleClient->post($url, $postData);
            $prekesJson = $res->getBody();

            $content = $prekesJson->getContents();
            if ($res->getStatusCode() == 500) {
                throw new CatalogErrorException($res->getReasonPhrase());
            }
            if ($res->getStatusCode() != 200) {
                throw new CatalogErrorException(substr($content, 0, self::ERROR_SIZE));
            }

            /** @var PrekesRestResult $prekesResult */
            $prekesResult = \GuzzleHttp\json_decode($content);
            if (isset($prekesResult->Prekes->PrekesList)) {
                return $prekesResult->Prekes->PrekesList;
            } else {
                $this->handlePrekesErrorResponse($prekesResult);
                return [];
            }

        } catch (CatalogErrorException $e ) {
            // šitas reikalingas tam, kad neperimtų catch (\Exception)
            throw $e;
        } catch (Exception $e) {
            $this->handlePrekesException($url, $postData, $e);
            return [];
        }
    }

    /**
     * @param string [] $dalisNomnrs
     * @param string $locale
     * @return KatalogasPreke[]
     * @throws CatalogErrorException
     */
    public function getPrekesOnly ($dalisNomnrs, $locale) {
        /** @var KatalogasPreke[] $prekes */
        $prekes = [];
        $gauta = false;
        $bandymu = 0;
        while ( !$gauta ) {
            try {
                $bandymu++;
                /** @var KatalogasPreke[] $prekes */
                $prekes = $this->getPrekesOnlyInner($dalisNomnrs, $locale);
                break;
            } catch ( CatalogErrorException $e  ) {
                if ($bandymu > self::MAX_UZKLAUSU_KATALOGUI) {
                    $this->logger->error('Nepavyko gauti porcijos prekių , atlikus ' . self::MAX_UZKLAUSU_KATALOGUI . ' užklausų');
                    // tris kart nepavykus, metam klaidą aukštyn
                    throw $e;
                } else {
                    $this->logger->notice('Bandysim dar kartą nes: ' . $e->getMessage());
                }
            } catch (Exception $e) {
                $nomnrs3 = array_slice ( $dalisNomnrs, 0 , min(3, count($dalisNomnrs)));
                $this->logger->error('Nenumatyta klaida imant porciją prekių [ pirmos trys:'.join( ',', $nomnrs3).'] iš katalogo: '.$e->getMessage() );
                break;
            }
        }
        return $prekes;
    }

    /**
     * @param string $url
     * @param array $postData
     */
    protected function handlePrekesException($url, $postData, Exception $e)
    {

        if (get_class($e) == ServerException::class || get_class($e) == ClientException::class) {
            /** @var ServerException $se */
            $se = $e;

            $resp = $se->getResponse();
            $message = "Klaida:" . $se->getMessage();
            $body = $resp->getBody();
            if (!empty($body)) {
                $content = $body->getContents();
                $message .= "\nAtsakymas:" . substr($content, 0, self::ERROR_SIZE);
            }
            if ( $se->getCode() == 500 ) {
                throw new CatalogErrorException($message);
            }
            throw new CatalogErrorException($message);
        } else if (get_class($e) == CatalogErrorException::class) {
            /** @var CatalogErrorException $ee */
            $ee = $e;
            throw $ee;
        } else {
            $errorMessage = "Nepavyko atidaryti [$url] su parametrais: \n"
                . substr( var_export($postData, true), 0, self::ERROR_SIZE ) . "\n"
                . "Klaidos pranešimas: " . $e->getMessage();
            throw new CatalogValidateException($errorMessage);
        }
    }

    /**
     * @param PrekesRestResult $restResult
     * @throws CatalogErrorException
     */
    protected function handlePrekesErrorResponse($restResult)
    {
        if (isset($restResult->errorMessage)) {
            throw new CatalogErrorException($restResult->errorMessage);
        } else {
            throw new CatalogErrorException('Neaiški katalogo klaida:' . substr(\GuzzleHttp\json_encode($restResult),
                    0, self::ERROR_SIZE));
        }
    }

    /**
     * @return mixed
     */
    public function getKatalogasRestBaseUrl()
    {
        return $this->katalogasRestBaseUrl;
    }

    /**
     * @param mixed $katalogasRestBaseUrl
     */
    public function setKatalogasRestBaseUrl($katalogasRestBaseUrl)
    {
        $this->katalogasRestBaseUrl = $katalogasRestBaseUrl;
    }

    /**
     * @return mixed
     */
    public function getKatalogasSiteBaseUrl()
    {
        return $this->katalogasSiteBaseUrl;
    }

    /**
     * @param mixed $katalogasSiteBaseUrl
     */
    public function setKatalogasSiteBaseUrl($katalogasSiteBaseUrl)
    {
        $this->katalogasSiteBaseUrl = $katalogasSiteBaseUrl;
    }

    /**
     * @param $uri
     * @param $filePath
     * @return int
     */
    public function downloadFile($uri, $filePath)
    {
        $url = $this->katalogasSiteBaseUrl . $uri;
        $response = $this->guzzleClient->get($url, ['save_to' => $filePath]);

        return $response->getStatusCode();
    }


    /**
     * @param string $uri
     * @param string $clientCode
     * @return string
     */
    public function fixClientCodeUri($uri, $clientCode)
    {
        return str_replace(self::CLIENT_CODE_PLACEHOLDER, $clientCode, $uri);
    }

}