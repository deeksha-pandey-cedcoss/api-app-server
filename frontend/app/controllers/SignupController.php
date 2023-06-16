<?php
namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {
        //    defalut action

    }
    public function registerAction()
    {
        $ch = curl_init();
        $url = "http://172.19.0.5/api/signup";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        if ($contents != null) {
            $this->response->redirect("login");
        } else {
            $this->response->redirect("signup");
        }
    }
}
