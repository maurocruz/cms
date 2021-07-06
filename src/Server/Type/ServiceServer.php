<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class ServiceServer {

    public function new($params): string {
        $tableHasPart = $params['providerType'];
        $idHasPart = $params['provider'];
        $data = Api::post('service',$params);
        $item = $data['id'];
        return "/admin/$tableHasPart/service?id=$idHasPart&item=$item";
    }

}