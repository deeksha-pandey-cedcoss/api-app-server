<?php

declare(strict_types=1);

namespace Tests\Unit;

use MyApp\Controllers\IndexController;
use MyApp\Controllers\LoginController;
use MyApp\Controllers\SignupController;

class IndexControllerTest extends AbstractUnitTest
//class UnitTest extends \PHPUnit\Framework\TestCase
{


    public function testsignup()
    {
        $mongo = new \MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );
        $input = [
            "id" => "111",
            "name" => "deeksha",
            "email" => "d@gmail.com",
            "pass" => "1",
            "appkey" => "deeksha",
            "secret" => "deeksha123",
            "time" =>time()
        ];

        $signup = new SignupController();
        $test = $signup->registerAction($input);
        $this->assertEquals($test, 1);
    }
    
}
