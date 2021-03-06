<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\Type\ImageObjectServer;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Sitemap;

class ImageObjectController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "groupBy" => "keywords", "orderBy" => "keywords" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("imageObject", $params3);
    }

    public function keywords($params): array {
        $keywords = isset($params['id']) ? urldecode($params['id']) : null;
        $params2 = [ "format" => "ItemList", "limit" => "none", "orderBy" => "uploadDate desc, keywords" ];
        if($keywords) {
            $params2['keywords'] = $keywords;
        } else {
            $params2['where'] = 'keywords is null';
        }
        $data['list'] = Api::get("imageObject", $params2);
        $data['paramsUrl'] = $params;
        return $data;
    }

    public function new($params = null) {
        return null;
    }

    public function edit(array $params): array {
        $value = [];
        $params2 = [ "properties" => "*,author" ];
        $params3 = array_merge($params2, $params);
        $data = Api::get("imageObject", $params3);
        if (isset($data[0])) {
            $value = $data[0];
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $value['info'] = (new ImageObjectServer())->getImageHasPartOf($id);
        }
        return $value;
    }

    public function saveSitemap() {
        $dataSitemap = null;
        $data = Api::get("imageObject", [ "properties" => "license", "orderBy" => "uploadDate" ]);
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
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
