<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\WebPageElement;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\WebSite\WebSiteController;

class WebPageElementController
{
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array {
    $params2 = [ "properties" => "*" ];
    $params3 = array_merge($params, $params2);
    $data = CmsFactory::request()->api()->get("webPageElement", $params3)->ready();
    return $data[0];
  }
	/**
	 * @throws DOMException
	 */
  public function saveSitemap() {
    (new WebSiteController())->saveSitemap();
  }
}
