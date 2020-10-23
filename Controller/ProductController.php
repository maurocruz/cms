<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Product;

class ProductController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "availability,additionalType", "orderBy" => "availability, dateModified desc, position" ];

        $params3 = $params ? array_merge($params, $params2) : $params2;

        return (new Product())->get($params3);
    }

    public function edit(array $params): array
    {
        $params2 = array_merge($params, [ "properties" => "*,image" ]);

        return (new Product())->get($params2);
    }

    public function new()
    {
        return [];
    }
}