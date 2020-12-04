<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Order;

class OrderController implements ControllerInterface
{

    public function index($params = null): array
    {
        $seller = $params['service'] ?? null;
        $params2 = [ "format" => "ItemList", "seller" => $seller ];
        $data = (new Order())->get($params2);
        var_dump($data);
        return $data;
    }

    public function edit(array $params): array
    {
        return [];
    }

    public function new($params = null)
    {
        $data = [];
        $item = $params['orderedItem'] ?? null;
        if ($item) {
            $itemType = $params['itemType'];
            $classType = "\\Plinct\\Api\\Type\\".ucfirst($itemType);
            $orderedItem = (new $classType())->get(["id" => $item]);
            $data['orderedItem'] = $orderedItem[0];
            return $data;
        }
        return null;
    }
}