<?php

namespace Matt\Php\Web\Login;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testRegex()
    {

        $path = "/products/12345/categories/abcde";

        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        $resutlt = preg_match($pattern, $path, $variables);

        self::assertEquals(1, $resutlt);

        var_dump($variables);

        array_shift($variables);
        var_dump($variables);

    }
}