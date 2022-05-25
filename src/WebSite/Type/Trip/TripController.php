<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Plinct\Cms\Server\Api;

class TripController
{
    /**
     * @param $params
     * @return array
     */
    public function index($params = null): array
    {
			$params2 = ['format'=>'ItemList', 'properties'=>'name,dateModified,identifier','orderBy'=>'dateModified desc'];
			$params3 = $params ?  array_merge($params2, $params) : $params2;
        return Api::get('trip',$params3);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'] ?? null;
        return Api::get('trip',['id'=>$id,'properties'=>'*,provider,image,identifier,subTrip']);
    }

    /**
     * @param $params
     * @return array
     */
    public function new($params = null): array
    {
        $provider = $params['provider'] ?? null;
        return Api::get('organization',['id'=>$provider,'properties'=>'name']);
    }
}
