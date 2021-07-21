<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class WebPageElementController implements ControllerInterface {

    public function index($params = null): array {
        return [];
    }

    public function new($params = null) {
        return null;
    }

    public function edit(array $params): array {
        $params2 = [ "properties" => "*" ];
        $params3 = array_merge($params, $params2);
        $data = Api::get("webPageElement", $params3);
        return $data[0];
    }

    public function saveSitemap($params = null) {
        $id = $params['id'] ?? null;
        $data = Api::get("webPageElement", ["id" => $id, "properties" => "isPartOf"]);
        if (!empty($data)) {
            (new WebPageController())->saveSitemap();
        }
    }
}