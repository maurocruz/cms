<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class OfferController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "properties" => "*,itemOffered" ];
        return Api::get("offer", $params2);
    }

    public function edit(array $params): array {
        $id = $params['id'];
        return Api::get("offer", [ "id" => $id, "properties" => "*,itemOffered"]);
    }

    public function new($params = null)  {
        return null;
    }
}
