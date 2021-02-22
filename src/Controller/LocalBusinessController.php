<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\LocalBusiness;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class LocalBusinessController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return (new LocalBusiness())->get($params3);
    }
    
    public function edit(array $params): array {
        $newParams = array_merge($params, [ "properties" => "*,location,address,organization,contactPoint,member,image" ]);
        return (new LocalBusiness())->get($newParams);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = (new LocalBusiness())->get([ "orderBy" => "dateModified desc", "properties" => "image,dateModified" ]);
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/localBusiness/$id",
                "lastmod" => DateTime::formatISO8601($value['dateModified']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-localBusiness.xml"))->saveSitemap($dataSitemap);
    }
}
