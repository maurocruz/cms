<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class ServiceController implements ControllerInterface {

    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "properties" => "provider,offers" ];
        return Api::get("service", $params2);
    }

    public function edit(array $params): array {
        $id = $params['id'];
        return Api::get("service", [ "id" => $id, "properties" => "*,provider,offers", "limit" => "none" ]);
    }

    public function new($params = null) {
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
