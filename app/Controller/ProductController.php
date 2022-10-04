<?php

namespace Matt\Php\Web\Login\Controller;

class ProductController 
{
    function categories(string $productId, string $categoryId):void
    {
        echo "PRODUCT $productId, CATEGORY $categoryId";
    }
}