<?php
/**
 * ValidateHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-09-04
 * Time: 08:41
 */

namespace Gt\Catalog\Utils;


class ValidateHelper
{
    const POSSIBLE_COUNTRIES=['LT', 'LV', 'PL', 'SE']; // will be used later

    public static function isValidPhone ($phone)
    {
        return preg_match('/^[0-9+\\-()[:space:]]+$/', $phone) == 1;
    }

    public static function isValidEmail($email) {
        return preg_match( '/^[[:alnum:]\\-._]+@[[:alnum:]\\-._]+$/', $email ) == 1;
    }

    public static function isValidOrderNumber($number) {
        return preg_match( '/^[[:alnum:]\\-_]{3,20}$/', $number )==1;
    }

    public static function isValidText($text) {
        // remove hacks
        if (
            strpos( $text, '/*' ) !== false or
            strpos( $text, '*/' ) !== false or
            strpos( $text, '--' ) !== false
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param $val
     * @return bool
     */
    public static function isValidFloat($val) {
        $val = str_replace(',','.', $val );
        return is_numeric($val);
    }

    /**
     * @param int $amount
     * @return bool
     */
    public static function isValidAmount($amount) {
        $i = intval($amount);
        return is_numeric($amount) and $amount == $i and $i > 0;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isValidName($name) {
        return preg_match( '/^[[:alnum:][:space:]\\-]+$/', $name ) == 1;
    }


    /**
     * @param string $countryCode
     * @return bool
     */
    public static function isValidCountryCode($countryCode) {
        return preg_match( '/^[[:alnum:]]{2}$/', $countryCode ) == 1;
    }

    /**
     * @param string $countryCode
     * @return bool
     */
    public static function isValidCompanyCode($companyCode) {
        return preg_match( '/^[[:alnum:]\\-]{2,20}$/', $companyCode ) == 1;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public static function isValidTag ($tag) {
        return !empty($tag)
        && preg_match( '/^[[:alnum:]\\-._]{2,30}$/', $tag ) == 1;
    }

    /**
     * @param string $str
     * @param string[] $substrs
     * @return bool
     */
    public static function endsWithAnyOf ( $str, $substrs ) {
        foreach ($substrs as $substr ) {
            if ( str_ends_with($str, $substr)) {
                return true;
            }
        }
        return false;
    }
}