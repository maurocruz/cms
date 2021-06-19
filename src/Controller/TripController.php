<?php

namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;
use Plinct\PDO\PDOConnect;

class TripController implements ControllerInterface {

    public function index($params = null): array {
        $provider = $params['provider'] ?? null;
        $data = Api::get('organization',['id'=>$provider,'properties'=>'name']);
        $data[0]['trip'] = Api::get('trip',['format'=>'ItemList','provider'=>$provider,'properties'=>'name,dateModified','orderBy'=>'dateModified']);
        return $data[0];
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