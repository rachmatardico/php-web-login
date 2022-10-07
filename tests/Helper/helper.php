<?php

namespace Matt\Php\Web\Login\App
{
    function header(string $value)
    {
        echo $value;
    }
}

namespace Matt\Php\Web\Login\Service
{
    function setcookie(string $name, string $value)
    {
        echo "$name : $value";
    }
}