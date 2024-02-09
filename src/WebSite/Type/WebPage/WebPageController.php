<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\WebPage;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\Sitemap;
use Plinct\Cms\WebSite\Type\WebSite\WebSiteController;

class WebPageController
{
  /**
   * @param array|null $params
   * @return array
   */
  public function index(?array $params = []): array
  {
    $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ], $params);
    return CmsFactory::request()->api()->get("webPage", $params2)->ready();
  }
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $params2 = array_merge($params, [ "properties" => "*,hasPart,isPartOf" ]);
    $data = CmsFactory::request()->api()->get("webPage", $params2)->ready();
    return $data[0];
  }
  /**
   * @return array
   */
  public function sitemap(): array {
    return (new Sitemap())->getSitemaps();
  }
	/**
	 * @throws DOMException
	 */
  public function saveSitemap()
  {
	  (new WebSiteController())->saveSitemap();
  }
}
