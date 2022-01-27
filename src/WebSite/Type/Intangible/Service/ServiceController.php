<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Service;

use Plinct\Cms\Server\Api;

class ServiceController
{
    /**
     * @return array
     */
    public function index(): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "provider,offers" ];
        return Api::get("service", $params2);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'];
        return Api::get("service", [ "id" => $id, "properties" => "*,provider,offers", "limit" => "none" ]);
    }

    /**
     * @return null
     */
    public function new()
    {
        return null;
    }

    /**
     * @param $params
     * @return array
     */
    public function order($params): array
    {
        $id = $params['id'];
        $params2 = [ "id" => $id ];
        $dataService = Api::get("service", $params2);
        $valueService = $dataService[0];
        $params3 = [ "format" => "ItemList", "orderedItem" => $id, "orderedItemType" => "service", "properties" => "*,seller", "orderBy" => "orderDate" ];
        $valueService['orders'] = Api::get("order", $params3);
        return $valueService;
    }

    public function provider($params): array {
        $id = $params['id'];
        $params2 = [ "format" => "ItemList", "provider" => $id, "properties" => "*,provider" ];
        return Api::get("service", $params2);
    }
}
