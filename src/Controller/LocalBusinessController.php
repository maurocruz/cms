<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class LocalBusinessController
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "additionalType,dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("localBusiness",$params3);
    }
    
    public function edit(array $params): array {
        $newParams = array_merge($params, [ "properties" => "*,location,address,organization,contactPoint,member,image" ]);
        return Api::get("localBusiness",$newParams);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = Api::get("localBusiness",[ "orderBy" => "dateModified desc", "properties" => "image,dateModified" ]);
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/localBusiness/$id",
                "lastmod" => DateTime::formatISO8601($value['dateModified']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-localBusiness.xml"))->saveSitemap($dataSitemap);
    }
}
