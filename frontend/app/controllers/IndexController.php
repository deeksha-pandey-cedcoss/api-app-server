<?php
namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->response->redirect("signup");
    }
}
