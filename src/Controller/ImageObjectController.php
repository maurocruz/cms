<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\ImageObjectServer;
use Plinct\Tool\Sitemap;

class ImageObjectController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "groupBy" => "keywords", "limit" => "none", "orderBy" => "keywords" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("imageObject", $params3);
    }

    public function keywords($params): array {
        $keywords = urldecode($params['id']);
        $params2 = [ "format" => "ItemList", "keywords" => $keywords, "limit" => "none", "orderBy" => "uploadDate desc, keywords" ];
        $data['list'] = Api::get("imageObject", $params2);
        $data['paramsUrl'] = $params;
        return $data;
    }

    public function new($params = null) {
        return null;
    }

    public function edit(array $params): array {
        $params2 = [ "properties" => "*,author" ];
        $params3 = array_merge($params2, $params);
        $data = Api::get("imageObject", $params3);
        $value = $data[0];
        $id = PropertyValue::extractValue($value['identifier'], "id");
        $value['info'] = (new ImageObjectServer())->getImageHasPartOf($id);
        return $value;
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = Api::get("imageObject", [ "properties" => "license", "orderBy" => "uploadDate" ]);
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
            $imageLoc = App::$HOST . str_replace(" ", "%20", $value['contentUrl']);
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/imageObject/$id",
                "image" => [
                    [ "contentUrl" => $imageLoc, "license" => $value['license'] ]
                ]
            ];
        }
        (new Sitemap("sitemap-imageObject.xml"))->saveSitemap($dataSitemap);
    }
}
