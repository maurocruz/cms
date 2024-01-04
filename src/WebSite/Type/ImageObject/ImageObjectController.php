<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\ImageObject;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\Type\ImageObjectServer;
use Plinct\Tool\Sitemap;

class ImageObjectController
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
    $data = CmsFactory::request()->api()->get("imageObject", $params3)->ready();
    if (isset($data[0])) {
      $value = $data[0];
      $value['info'] = (new ImageObjectServer())->getImageHasPartOf($value['idimageObject']);
    }
    return $value;
  }

  public function saveSitemap() {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get("imageObject", [ "properties" => "license", "orderBy" => "uploadDate" ])->ready();
    foreach ($data as $value) {
      $id = $value['idimageObject'];
      $imageLoc = App::getURL() . str_replace(" ", "%20", $value['contentUrl']);
      $dataSitemap[] = [
        "loc" => App::getURL() . "/t/imageObject/$id",
        "image" => [
          [ "contentUrl" => $imageLoc, "license" => $value['license'] ]
        ]
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-imageObject.xml"))->saveSitemap($dataSitemap);
  }
}
