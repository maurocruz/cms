<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class TravelAgencyController implements ControllerInterface {

    public function index($params = null): array {
        return Api::get('organization', ['format'=>'ItemList','additionalTypeLike'=>'TravelAgency','properties'=>'name']);
    }

    public function edit(array $params): array {
        $id = $params['id'] ?? null;
        $data = Api::get('organization',['id'=>$id,'properties'=>'name']);
        return $data[0];
    }

    public function new($params = null)
    {
        // TODO: Implement new() method.
    }
}