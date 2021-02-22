<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Place;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PlaceController implements ControllerInterface
{
    public function index($params = null): array {
        $params = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ], $params);
        return (new Place())->get($params);
    }
    
    public function new($params = null): bool {
        return true;
    }
    
    public function edit(array $params): array {
        $params= array_merge($params, [ "properties" => "address,image" ]);
        return (new Place())->get($params);
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = (new Place())->get([ "orderBy" => "dateModified desc", "properties" => "*,image" ]);
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
