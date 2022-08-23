<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class ServiceServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string {
        $tableHasPart = $params['providerType'];
        $idHasPart = $params['provider'];
        $data = Api::post('service',$params);
        $item = $data['id'];
        return "/admin/$tableHasPart/service?id=$idHasPart&item=$item";
    }
}
