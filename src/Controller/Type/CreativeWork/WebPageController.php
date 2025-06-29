<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Cms\Helpers\Sitemap;

class WebPageController implements TypeControllerInterface
{
	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function index(?array $params = []): bool
	{
		$idwebSite = $params['idwebSite'] ?? null;
		$data = $idwebSite ? CmsFactory::model()->api()->get('webSite', ['idwebSite' => $idwebSite])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('index')->setData($value)->ready();
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function new(?array $params = []): bool
	{
		$idwebSite = $params['idwebSite'] ?? null;
		$data = $idwebSite ? CmsFactory::model()->api()->get('webSite', ['idwebSite' => $idwebSite])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('new')->setData($value)->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$params2 = array_merge($params, ["properties" => "hasPart,isPartOf,propertyValue"]);
		$data = CmsFactory::model()->api()->get("webPage", $params2)->ready();
		if (isset($data['status']) && $data['status'] === 'fail') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
			return false;
		}
		return isset($data[0]) && is_array($data[0]) && CmsFactory::view()->webSite()->type('webPage')->setMethodName('edit')->setData($data[0])->ready();
	}

	/**
	 * @throws Exception
	 */
	public function sitemap(array $params = []): bool
	{
		$data = CmsFactory::model()->type('webPage')->get(['orderBy'=>'dateModified','ordering'=>'desc','fields'=>'name,url,dateModified']);
		$dataSitemap = Sitemap::buildSimpleSitemap($data);
		return CmsFactory::view()->webSite()->type('webPage')->setData($dataSitemap)->setQueryParams($params)->setMethodName('sitemap')->ready();
	}
}
