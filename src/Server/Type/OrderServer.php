<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;

class OrderServer {

    public function new($params): string {
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
        return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $id;
    }
}