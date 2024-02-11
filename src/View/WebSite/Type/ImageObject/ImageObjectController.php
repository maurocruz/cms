<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\ImageObject;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\Request\Server\Type\ImageObjectServer;
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
    $data = CmsFactory::request()->api()->get("imageObject", ['properties'=>'license','orderBy'=>'uploadDate','limit'=>'none'])->ready();
    foreach ($data as $value) {
      $id = $value['idimageObject'];
      $imageLoc = ImageObjectController . phpApp::getURL() . str_replace(" ", "%20", $value['contentUrl']);
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
