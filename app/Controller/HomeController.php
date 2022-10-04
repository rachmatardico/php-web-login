<?php

namespace Matt\Php\Web\Login\Controller;

use Matt\Php\Web\Login\App\View;

class HomeController
{
    function index():void
    {
        $model = [
            "title"     => "Belajar PHP MVC",
            "content"   => "Selamat belajar PHP MVC dari Programmer Zaman Now"
        ];

        View::render('Home/index', $model);
    }

    function hello():void
    {
        echo "HomeController.hello()";
    }
    
    function world():void
    {
        echo "HomeController.world()";
    }

    function about():void
    {
        echo "Student : Rachmat Ardico Perdana";
    }

    function login():void
    {
        $request = [
            "username"  => $_POST['username'],
            "password"  => $_POST['password']
        ];

        $user = [

        ];

        $response = [
            "message"     => "Login sukses"
        ];
        // kirimkan response ke view
    }
}