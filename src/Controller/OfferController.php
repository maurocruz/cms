<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Offer;

class OfferController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "*" ];

        return (new Offer())->get($params2);
    }

    public function edit(array $params): array
    {
        return [];
    }

    public function new($params = null)
    {
        return null;
    }
}