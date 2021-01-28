<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Offer;

class OfferController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "*,itemOffered" ];

        return (new Offer())->get($params2);
    }

    public function edit(array $params): array
    {
        $id = $params['id'];

        return (new Offer())->get([ "id" => $id, "properties" => "*,itemOffered"]);
    }

    public function new($params = null)
    {
        return null;
    }
}