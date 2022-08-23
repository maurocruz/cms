<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\TravelAgency;

use Plinct\Cms\Request\Api;

class TravelAgencyController
{
    /**
     * @return array
     */
    public function index(): array
    {
        return Api::get('organization', ['format'=>'ItemList','additionalTypeLike'=>'TravelAgency','properties'=>'name']);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['idtravelAgency'] ?? null;
        $data = Api::get('organization',['id'=>$id,'properties'=>'name']);
        return $data[0];
    }

    /**
     * @param $params
     * @return void
     */
    public function new($params = null)
    {
        // TODO: Implement new() method.
    }
}
