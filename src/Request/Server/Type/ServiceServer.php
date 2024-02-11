<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server\Type;

use Plinct\Cms\Controller\CmsFactory;

class ServiceServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string {
        $tableHasPart = $params['providerType'];
        $idHasPart = $params['provider'];
        $data = CmsFactory::request()->api()->post('service',$params)->ready();
        $item = $data['id'];
        return "/admin/$tableHasPart/service?id=$idHasPart&item=$item";
    }
}
