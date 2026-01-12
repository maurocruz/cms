<?php
namespace Plinct\Cms\Controller\Type\LocalBusiness;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\Organization\OrganizationController;

class LocalBusinessController extends OrganizationController
{
	public function __construct(string $type = 'localBusiness')
	{
		parent::__construct($type);
	}

	/**
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		$organization = $params['organization'] ?? null;
		$data = [];
		if ($organization) {
			$data = CmsFactory::model()->type('localBusiness')->get(['organization' => $organization,'properties'=>'organization']);
		}
		return CmsFactory::view()->webSite()->type('localBusiness')->setMethodName('index')->setData($data)->ready();
	}

	/**
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$idlocalBusiness = $params['idlocalBusiness'] ?? null;
		$data = [];
		if ($idlocalBusiness) {
			$data = CmsFactory::model()->type('localBusiness')->get(['idlocalBusiness' => $idlocalBusiness]);
		}
		return CmsFactory::view()->webSite()->type('localBusiness')->setData($data)->setMethodName('edit')->ready();
	}
}
