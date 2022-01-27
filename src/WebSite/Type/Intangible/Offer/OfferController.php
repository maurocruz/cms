<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Offer;

use Plinct\Cms\Server\Api;

class OfferController
{
    /**
     * @return array
     */
    public function index(): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "*,itemOffered" ];
        return Api::get("offer", $params2);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'];
        return Api::get("offer", [ "id" => $id, "properties" => "*,itemOffered"]);
    }

    /**
     * @return null
     */
    public function new()
    {
        return null;
    }
}
