<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class WebPageController implements ControllerInterface
{   
    public function index($params = null): array {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ], $params);
        return Api::get("webPage", $params2);
    }
    
    public function edit(array $params): array {
        $params2 = array_merge($params, [ "properties" => "alternativeHeadline,hasPart" ]);
        return Api::get("webPage", $params2);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function sitemap($params): array {
        return (new \Plinct\Cms\Server\Sitemap())->getSitemaps();
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = Api::get("webPage", ["properties" => "url,dateModified", "orderBy" => "dateModified desc"]);
        foreach ($data as $value) {
            $dataSitemap[] = [
                "loc" => App::$HOST . $value['url'],
                "lastmod" => DateTime::formatISO8601($value['dateModified'])
            ];
        }
        (new Sitemap("sitemap-webPage.xml"))->saveSitemap($dataSitemap);
    }
}
