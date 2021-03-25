<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class ProductController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "properties" => "availability,additionalType", "orderBy" => "availability, dateModified desc, position" ];
        $params3 = $params ? array_merge($params, $params2) : $params2;
        return Api::get("product", $params3);
    }

    public function edit(array $params): array {
        $params2 = array_merge($params, [ "properties" => "*,image" ]);
        return Api::get("product", $params2);
    }

    public function new($params = null): array {
        return [];
    }
}