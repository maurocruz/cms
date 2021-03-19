<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PlaceController implements ControllerInterface
{
    public function index($params = null): array {
        $params = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ], $params);
        return Api::get("place", $params);
    }
    
    public function new($params = null): bool {
        return true;
    }
    
    public function edit(array $params): array {
        $params= array_merge($params, [ "properties" => "address,image" ]);
        return Api::get("place", $params);
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $params = [ "orderBy" => "dateModified desc", "properties" => "*,image" ];
        $data =  Api::get("place", $params);
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/place/$id",
                "lastmod" => DateTime::formatISO8601($value['dateModified']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-place.xml"))->saveSitemap($dataSitemap);
    }
}
