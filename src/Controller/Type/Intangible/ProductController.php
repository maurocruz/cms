<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ProductController implements TypeControllerInterface
{
	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		$manufacturer = $params['manufacturer'] ?? null;
		if ($manufacturer) {
			$dataThing = CmsFactory::model()->type('thing')->get(['idthing' => $manufacturer, 'hasPart'=>true]);
		}
		if (isset($dataThing[0]) && $manufacturer) {
			return CmsFactory::view()->webSite()->type('product')->setData($dataThing[0])->ready();
		} else {
			return CmsFactory::view()->fragment()->message()->noContent();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		return false;
	}
}
