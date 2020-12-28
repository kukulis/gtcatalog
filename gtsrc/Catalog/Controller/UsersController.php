<?php
/**
 * UsersController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:25
 */

namespace Gt\Catalog\Controller;


use Symfony\Component\HttpFoundation\Response;

class UsersController
{
    public function listAction() {
        return new Response('TODO list');
    }

    public function editFormAction() {
        return new Response('TODO edit form');
    }

    public function updateAction() {
        return new Response('TODO update');
    }

    public function addFormAction() {
        return new Response('TODO add form');
    }

    public function addAction() {
        return new Response('TODO add');
    }

}