<?php
namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Token\Parser;


session_start();

class ProductsController extends Controller
{
    public function indexAction()
    {
        $token = $_SESSION['token'];
        $pages = $_GET['pages'];

        $limit = $_GET['limit'];
        $url = "http://172.19.0.5/api/products?limit=$limit&&pages=$pages";
        $ch = curl_init();
        $header = [
            'Authorization:' . $token
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        $result = json_decode($result, true);

        if ($result == null) {
            echo "</h3>Not allowed before 30 sec.</h3>";
            die;
        } else {
            $this->view->data = $result;
        }
    }
    public function pagesAction()
    {
        // default action for pages
    }
    public function currentpageAction()
    {

        $pages = $_POST['pages'];

        $limit = $_GET['limit'];
        $token = $_SESSION['token'];

        $url = "http://172.19.0.5/api/products/?limit=$limit&&pages=$pages";
        $ch = curl_init();
        $header = [
            'Authorization:' . $token
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        print_r($result);
        die;
    }
}
