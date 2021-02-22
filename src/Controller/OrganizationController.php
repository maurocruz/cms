<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Organization;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController implements ControllerInterface
{
    public function index($params = null): array {
        $paramsSet = [ "format" => "ItemList", "properties" => "update_time", "orderBy" => "update_time", "ordering" => "desc" ];
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        return (new Organization())->get($paramsGet);
    }
    
    public function edit(array $params): array {
        $params = [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ];
        return (new Organization())->get($params);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = (new Organization())->get([ "properties" => "image,update_time", "orderBy" => "update_time desc" ]);
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/organization/$id",
                "lastmod" => DateTime::formatISO8601($value['update_time']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-organization.xml"))->saveSitemap($dataSitemap);
    }
}
