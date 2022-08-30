<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class ServiceServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string {
        $tableHasPart = $params['providerType'];
        $idHasPart = $params['provider'];
        $data = CmsFactory::request()->api()->post('service',$params);
        $item = $data['id'];
        return "/admin/$tableHasPart/service?id=$idHasPart&item=$item";
    }
}
