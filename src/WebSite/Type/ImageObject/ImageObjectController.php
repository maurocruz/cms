<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\ImageObject;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\Type\ImageObjectServer;
use Plinct\Cms\WebSite\Type\ControllerInterface;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Sitemap;

class ImageObjectController implements ControllerInterface
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
		return ['data'=>[]];
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
      $imageLoc = App::getURL() . str_replace(" ", "%20", $value['contentUrl']);
      $dataSitemap[] = [
        "loc" => App::getURL() . "/t/imageObject/$id",
        "image" => [
          [ "contentUrl" => $imageLoc, "license" => $value['license'] ]
        ]
      ];
    }
    (new Sitemap("sitemap-imageObject.xml"))->saveSitemap($dataSitemap);
  }
}
