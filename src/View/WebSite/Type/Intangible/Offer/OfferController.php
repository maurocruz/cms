<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\Intangible\Offer;

use Plinct\Cms\Controller\CmsFactory;

class OfferController
{
    /**
     * @return array
     */
    public function index(): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "*,itemOffered" ];
        return CmsFactory::request()->api()->get("offer", $params2)->ready();
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'];
        return CmsFactory::request()->api()->get("offer", [ "id" => $id, "properties" => "*,itemOffered"])->ready();
    }

    /**
     * @return null
     */
    public function new()
    {
        return null;
    }
}
