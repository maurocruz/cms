<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class RoleController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		$refererType = $params['refererType'] ?? null;
		$refererId = $params['refererId'] ?? null;
		if ($refererType && $refererId) {
			$dataRole = CmsFactory::model()->api()->get('role',[lcfirst($refererType) => $refererId, 'properties' => 'person,organization'])->ready();
		} else {
			$organization = $params['organization'] ?? null;
			$dataRole = CmsFactory::model()->api()->get('role', ['organization' => $organization, 'properties' => 'memberOf,member'])->ready();
		}
		if (isset($dataRole['status']) && $dataRole['status'] == 'fail') {
			return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_($dataRole['message'])));
		}
		return CmsFactory::view()->webSite()->type('role')->setData($dataRole)->setQueryParams($params)->setMethodName('index')->ready();
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('role')->setQueryParams($params)->setMethodName('new')->ready();
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		$idrole = $params['idrole'] ?? null;
		$dataRole = CmsFactory::model()->api()->get('role', ['idrole' => $idrole, 'properties'=>'memberOf,member'])->ready();
		return CmsFactory::view()->webSite()->type('role')->setData($dataRole)->setQueryParams($params)->setMethodName('edit')->ready();
	}
}
