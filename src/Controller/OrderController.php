<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Order;

class OrderController implements ControllerInterface
{

    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList" ];

        return (new Order())->get($params2);
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
            $orderedItem = (new $classType())->get(["id" => $item, "properties" => "*,offers"]);
            $data['orderedItem'] = $orderedItem[0];
            return $data;
        }
        return null;
    }
}