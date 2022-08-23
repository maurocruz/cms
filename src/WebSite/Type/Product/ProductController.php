<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Product;

use Plinct\Cms\Request\Api;

class ProductController
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "availability,additionalType", "orderBy" => "availability, dateModified desc, position" ];
        $params3 = $params ? array_merge($params, $params2) : $params2;
        return Api::get("product", $params3);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = array_merge($params, [ "properties" => "*,image,manufacturer,offers" ]);
        return Api::get("product", $params2);
    }

    /**
     * @return array
     */
    public function new(): array
    {
        return [];
    }
}
