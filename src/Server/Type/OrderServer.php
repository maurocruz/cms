<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;
use Plinct\Cms\Server\ServerAbstract;

class OrderServer extends ServerAbstract
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string
    {
        $seller = $params['seller'];
        $sellerType = $params['sellerType'];
        // insert new order
        $data = Api::post('order', $params);
        $id = $data['id'];
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $id);
        $history->setSummary("Created new order");
        $history->register("CREATED");
        // RESPONSE
        $redirect = "/admin/" . lcfirst($sellerType) . "/order?id=$seller&item=$id";
        return parent::response($data, $redirect);
    }

    /**
     * @param $params
     * @return mixed|void
     */
    public function edit($params)
    {
        // HISTORY
        $dataOld = Api::get('order', [ "id" => $params['id'] ]);
        parent::setHistoryUpdateWithDifference('order', $params['id'], $dataOld[0], $params);
        // RESPONSE
        return parent::response(Api::put('order',$params));
    }

    /**
     * @param $params
     * @return string
     */
    public function erase($params): string
    {
        $idorder = $params['id'] ?? $params['idIsPartOf'] ?? $params['idorder'];
        Api::delete('order', [ "idorder" => $idorder ]);

        $tableHasPart = $params['tableHasPart'] ?? lcfirst($params['sellerType']);
        $idHasPart = $params['idHasPart'] ?? $params['seller'];

        return "/admin/$tableHasPart/order?id=$idHasPart";
    }
}
