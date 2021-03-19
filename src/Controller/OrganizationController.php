<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController implements ControllerInterface
{
    public function index($params = null): array {
        $paramsSet = [ "format" => "ItemList", "properties" => "update_time", "orderBy" => "update_time", "ordering" => "desc" ];
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        return Api::get("organization", $paramsGet);
    }
    
    public function edit(array $params): array {
        $params = [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ];
        return Api::get("organization", $params);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap() {
        $dataSitemap = null;
        $params = [ "properties" => "image,update_time", "orderBy" => "update_time desc" ];
        $data = Api::get("organization", $params);
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
