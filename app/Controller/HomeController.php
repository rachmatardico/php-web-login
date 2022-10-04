<?php

namespace Matt\Php\Web\Login\Controller;

use Matt\Php\Web\Login\App\View;

class HomeController
{
    function index()
    {
        View::render('Home/index', [
            "title" => "PHP login Management"
        ]);
    }
}