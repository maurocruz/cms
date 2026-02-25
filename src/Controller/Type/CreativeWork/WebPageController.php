<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class WebPageController extends CreativeWorkController implements TypeControllerInterface
{
	/**
	 */
	public function __construct()
	{
		parent::__construct('webPage');
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function index(?array $params = []): bool
	{
		$webSiteThing = $params['webSite'] ?? null;
		$data = $webSiteThing ? CmsFactory::model()->api()->get('webSite', ['thing' => $webSiteThing])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('index')->setData($value)->ready();
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function new(?array $params = []): bool
	{
		$webSiteThing = $params['webSite'] ?? null;
		$data = $webSiteThing ? CmsFactory::model()->api()->get('webSite', ['thing' => $webSiteThing])->ready() : null;
		$value = $data[0] ?? null;
		return CmsFactory::view()->webSite()->type('webPage')->setMethodName('new')->setData($value)->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$params2 = array_merge($params, ["properties" => "isPartOf,propertyValue", "typeHasPart" => "webSite"]);
		$data = CmsFactory::model()->api()->get("webPage", $params2)->ready();
		if (isset($data['status']) && $data['status'] === 'fail') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
			return false;
		}
		return isset($data[0]) && is_array($data[0]) && CmsFactory::view()->webSite()->type('webPage')->setMethodName('edit')->setData($data[0])->ready();
	}
}
