<?php
namespace Plinct\Cms\Controller\Type\Action;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\ThingController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ActionController extends ThingController implements TypeControllerInterface
{
	/**
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		$data = [];
		$object = $params['object'] ?? null;
		if ($object) {
			$data = CmsFactory::model()->type('thing')->get(['idthing' => $object,'hasPart'=>'']);
			if (isset($data[0])) {
				$data = $data[0];
			}
		}
		return CmsFactory::view()->webSite()->type('action')->setData($data)->setQueryParams($params)->ready();
	}

	/**
	 * @throws Exception
	 */
	public function new(array $params): bool
	{
		$data = [];
		$object = $params['object'] ?? null;
		if ($object) {
			$data = CmsFactory::model()->type('thing')->get(['idthing' => $object,'hasPart'=>'']);
			if (isset($data[0])) {
				$data = $data[0];
			}
		}
		return CmsFactory::view()->webSite()->type('action')->setData($data)->setQueryParams($params)->setMethodName('new')->ready();
	}

	/**
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->type('action')->get($params + ['properties'=>'object']);
		return CmsFactory::view()->webSite()->type('action')->setData($data)->setQueryParams($params)->setMethodName('edit')->ready();
	}
}
