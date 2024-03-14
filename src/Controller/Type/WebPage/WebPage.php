<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type\WebPage;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Request\Server\Sitemap;
use Plinct\Cms\Controller\WebSite\Type\WebSite\WebSiteController;

class WebPage
{
	/**
	 * @param array|null $params
	 * @return bool
	 */
  public function index(?array $params = []): bool {
		$idwebSite = $params['idwebSite'] ?? null;
	  $data = $idwebSite ? CmsFactory::model()->api()->get('webSite',['idwebSite'=>$idwebSite])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('index')->setData($value)->ready();
  }

	public function new(?array $params = []): bool {
		$idwebSite = $params['idwebSite'] ?? null;
		$data = $idwebSite ? CmsFactory::model()->api()->get('webSite',['idwebSite'=>$idwebSite])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('new')->setData($value)->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
  public function edit(array $params): bool {
    $params2 = array_merge($params, [ "properties" => "image,hasPart,isPartOf" ]);
    $data = CmsFactory::model()->api()->get("webPage", $params2)->ready();
		if (isset($data['status']) && $data['status'] === 'fail') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
			return false;
		}
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('edit')->setData($data[0])->ready();
  }
  /**
   * @return array
   */
  public function sitemap(): array {
    return (new Sitemap())->getSitemaps();
  }
	/**
	 */
  public function saveSitemap()
  {
	  (new WebSiteController())->saveSitemap();
  }
}
