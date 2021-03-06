<?php

namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class TripController implements ControllerInterface {

    public function index($params = null): array {
        $provider = $params['provider'] ?? null;
        $data = Api::get('organization',['id'=>$provider,'properties'=>'name']);
        $value = $data[0];
        $value['trip'] = Api::get('trip',['format'=>'ItemList','provider'=>$provider,'properties'=>'name,dateModified,identifier','orderBy'=>'dateModified desc']);
        return $value;
    }

    public function edit(array $params): array {
        $id = $params['id'] ?? null;
        return Api::get('trip',['id'=>$id,'properties'=>'*,provider,image,identifier,subTrip']);
    }

    public function new($params = null) {
        $provider = $params['provider'] ?? null;
        return Api::get('organization',['id'=>$provider,'properties'=>'name']);
    }
}