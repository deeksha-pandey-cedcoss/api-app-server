<?php
namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;

session_start();
class LoginController extends Controller
{
    public function indexAction()
    {
        // login
    }
    public function loginAction()
    {
        $ch = curl_init();
        $url = "http://172.19.0.5/api/login";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $contents = curl_exec($ch);

        $contents = json_decode($contents, true);
        $appkey = $contents['appkey'];
        $secret = $contents['secret'];
        $_SESSION['passphrase'] = $appkey . $secret;
        if ($contents != null) {
            $_SESSION['details'] = $contents['name'] . "/" . $contents['email'] . "/" .
                $contents['password'] . "/" . $contents['appkey'] . "/" . $contents['secret'] ."/". $contents['time'];
            $signer  = new Hmac();
            $builder = new Builder($signer);

            $now        = new DateTimeImmutable();
            $issued     = $now->getTimestamp();
            $notBefore  = $now->modify('-1 minute')->getTimestamp();
            $expires    = $now->modify('+30 minute')->getTimestamp();
            $passphrase =  $_SESSION['passphrase'];
            $builder
                ->setSubject($_SESSION['details'])
                ->setContentType('application/json')
                ->setExpirationTime($expires)
                ->setId('abcd123456789')
                ->setIssuedAt($issued)
                ->setNotBefore($notBefore)
                ->setPassphrase($passphrase);
            $tokenObject = $builder->getToken();

            echo 'This is the token for role is ' . PHP_EOL;
            echo $tokenObject->getToken();
            $token = $tokenObject->getToken();

            $_SESSION['token'] = $token;
            $this->response->redirect("products");
        } else {
            echo "Invalid Credentilas";
            die;
            $this->response->redirect("login");
        }
    }
    public function logoutAction()
    {
        session_unset();
        $this->response->redirect("login");
    }
}
