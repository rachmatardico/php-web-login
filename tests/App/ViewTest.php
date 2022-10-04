<?php

namespace Matt\Php\Web\Login\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender()
    {
        View::render('Home/index', [
            "PHP login Management"
        ]);

        $this->expectOutputRegex('[PHP Login Management]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}