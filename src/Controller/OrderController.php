<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Order;

class OrderController implements ControllerInterface
{

    public function index($params = null): array
    {
        $search = $params['search'] ?? null;

        $paramsSearch = $search ? [ "nameLike" => $search ] : [];

        $params2 = [ "format" => "ItemList", "properties" => "*", "orderBy" => "paymentDueDate DESC, orderStatus DESC", "limit" => "none" ];

        $params3 = array_merge($params2, $paramsSearch);

        return (new Order())->get($params3);
    }

    public function edit(array $params): array
    {
        $params2 = [ "id" => $params['id'], "properties" => "*,customer,seller,orderedItem,partOfInvoice,history" ];

        return $data = (new Order())->get($params2);
    }

    public function new($params = null): ?array
    {
        $data = [];
        $item = $params['orderedItem'] ?? null;
        if ($item) {
            $itemType = $params['orderedItemType'];
            $classType = "\\Plinct\\Api\\Type\\".ucfirst($itemType);
            $orderedItem = (new $classType())->get(["id" => $item, "properties" => "*,offers,provider"]);
            $data['orderedItem'] = $orderedItem[0];
            return $data;
        }
        return null;
    }
}