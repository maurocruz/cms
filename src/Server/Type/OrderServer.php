<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;

class OrderServer {

    public function new($params): string {
        $seller = $params['seller'];
        $sellerType = $params['sellerType'];
        // insert new order
        $data = Api::post('order', $params);
        $id = $data['id'];
        // insert new history
        $historyParams = [
            "tableHasPart" => "order",
            "idHasPart" => $id,
            "action" => "CREATED",
            "summary" => _("Created new order"),
            "datetime" => date("Y-m-d H:i:s"),
            "user" => App::getUserLoginId()
        ];
        Api::post('history', $historyParams);
        return "/admin/" . lcfirst($sellerType) . "/order?id=$seller&item=$id";
    }
}
