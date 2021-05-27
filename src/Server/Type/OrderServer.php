<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\ServerAbstract;

class OrderServer extends ServerAbstract {

    public function new($params): string {
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

    public function edit($params) {
        // HISTORY
        $dataOld = Api::get('order', [ "id" => $params['id'] ]);
        parent::setHistoryUpdateWithDifference('order', $params['id'], $dataOld[0], $params);
        // RESPONSE
        return parent::response(Api::put('order',$params));
    }

    public function erase($params) {
        $idorder = $params['id'];
        $tableHasPart = $params['tableHasPart'] ?? lcfirst($params['sellerType']);
        $idHaspart = $params['seller'];
        $redirect = "/admin/$tableHasPart/order?id=$idHaspart";
        return parent::response(Api::delete('order', [ "id" => $idorder ]), $redirect);
    }
}
