<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PersonController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("person", $params3);
    }

    public function edit(array $params): array {
        $params = array_merge($params, [ "properties" => "*,contactPoint,address,image" ]);
        return Api::get("person", $params);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap() {
        $dataSitemap = null;
        $params = [ "orderBy" => "dateModified desc", "properties" => "dateModified,image" ];
        $data = Api::get("person", $params);
        $loc = App::$HOST ."/t/Person/";
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $lastmod = DateTime::formatISO8601($value['dateModified']);
            $dataSitemap[] = [
                "loc" => $loc.$id,
                "lastmod" => $lastmod,
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-person.xml"))->saveSitemap($dataSitemap);
    }
}
