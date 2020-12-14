<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Order;
use Plinct\Api\Type\Service;

class ServiceController implements ControllerInterface
{

    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList" ];
        return (new Service())->get($params2);
    }

    public function edit(array $params): array
    {
        $id = $params['id'];
        return (new Service())->get([ "id" => $id, "properties" => "*,provider,offers" ]);
    }

    public function new($params = null)
    {
        return null;
    }

    public function order($params)
    {
        $id = $params['id'];
        $params2 = [ "id" => $id ];
        $dataService = (new Service())->get($params2);
        $valueService = $dataService[0];

        $params3 = [ "format" => "ItemList", "orderedItem" => $id, "orderedItemType" => "service", "properties" => "*,seller", "orderBy" => "orderDate" ];
        $valueService['orders'] = (new Order())->get($params3);

        return $valueService;
    }
}