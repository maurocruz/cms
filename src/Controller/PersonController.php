<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Person;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PersonController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return (new Person())->get($params3);
    }

    public function edit(array $params): array {
        $params = array_merge($params, [ "properties" => "*,contactPoint,address,image" ]);
        return (new Person())->get($params);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap($params) {
        $dataSitemap = null;
        $data = (new Person())->get([ "orderBy" => "dateModified desc", "properties" => "dateModified,image" ]);
        $loc = App::$HOST ."/t/Person/";
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
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
