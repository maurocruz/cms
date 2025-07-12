<?php

namespace Plinct\Cms\Controller\Type\Product;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\ThingController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ProductController extends ThingController implements TypeControllerInterface
{
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('product')->ready();
	}

	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('product')->setMethodName('new')->ready();
	}

	/**
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$idproduct = $params['idproduct'] ?? null;
		$data = [];
		if ($idproduct) {
			$data = CmsFactory::model()->type('product')->get(['idproduct' => $idproduct]);
		}
		return CmsFactory::view()->webSite()->type('product')->setData($data)->setMethodName('edit')->ready();
	}
}
