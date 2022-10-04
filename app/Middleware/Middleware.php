<?php

namespace Matt\Php\Web\Login\Middleware;

interface Middleware
{
    function before():void;
}