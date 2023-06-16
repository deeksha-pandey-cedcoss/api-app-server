<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Collection\Manager;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;


define("BASE_PATH", __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

session_start();

// Use Loader() to autoload our model
$container = new FactoryDefault();
$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->testing_new;
    },
    true
);
$container->set(
    'collectionManager',
    function () {
        return new Manager();
    }
);
$app = new Micro($container);
// Define the routes here

$app->post(
    '/api/signup',
    function () {
        $payload = [
            "name" => $_POST['name'],
            "email" => $_POST['email'],
            "password" => $_POST['password'],
            "appkey" => $_POST['name'],
            "secret" => $_POST['name'] . "123",
            "time" => time(),
        ];
        $collection = $this->mongo->Users;
        $status = $collection->insertOne($payload);
        print_r($status);
    }
);


$app->post(
    '/api/login',
    function () {
        $collection = $this->mongo->Users;
        $m = $collection->findOne(["email" => $_POST['email'], "password" => $_POST['password']]);
        return json_encode($m);
    }
);




$app->get(
    '/api/products',
    function () use ($app) {

        $headers = apache_request_headers();
        $arr = (array)$headers;
        $tokenr = $arr['Authorization'];
        if ($tokenr != "") {

            $tokenReceived =  $tokenr;
            $signer     = new Hmac();

            $passphrase =  $_SESSION['passphrase'];


            $parser      = new Parser();

            $tokenObject = $parser->parse($tokenReceived);
            $token = $tokenObject->getToken();

            $exp = $tokenObject->getClaims()->getPayload()['iat'];
            $sub = $tokenObject->getClaims()->getPayload()['sub'];

            $validator = new Validator($tokenObject, 100); // allow for a time shift of 100

            // // Throw exceptions if those do not validate
            $validator
                ->validateExpiration($exp);


            $sub = explode("/", $sub);
            $collection = $this->mongo->Users;
            $m = $collection->findOne(
                [
                    "name" => $sub[0], "email" => $sub[1],
                    "password" => $sub[2], "appkey" => $sub[3], "secret" => $sub[4]
                ]
            );
            if (time() - $m->time < 30) {
                echo "</h3>Not allowed before 30 sec.</h3>";
                die;
            } elseif ($m !== null) {

                if ($_GET['limit'] == "") {
                    $_GET['limit'] = 5;
                }
                $options = [
                    "limit" => $_GET['limit'],
                    "page" => $_GET['pages']
                ];

                $collection = $this->mongo->products->find(
                    array(),
                    ['limit' => (int)$options['limit'], 'skip' => (int)$options['limit'] * (int)$options['page']]
                );

                // update the last visit time
                $result = $this->mongo->Users->updateOne(["email" => $m->email], ['$set' => ['time' => time()]]);
                $data = [];

                foreach ($collection as $robot) {
                    $data[] = [
                        'id'   => $robot->_id,
                        'id'   => $robot->id,
                        'name' => $robot->name,
                        'price' => $robot->price,
                    ];
                }
                print_r(json_encode($data));
            } else {
                http_response_code(403);

                echo '<h3>You are forbidden!</h3>';
                echo "<h3>Unauthorised response acess (Wrong access token detected)</h3>";
                die;
            }
        }
    }

);


$app->handle($_SERVER['REQUEST_URI']);
