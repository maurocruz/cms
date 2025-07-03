<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;

class CertificationController extends CreativeWorkController
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct('certification');
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('certification')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('certification')->setMethodName('new')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->type('certification')->get($params);
		return CmsFactory::view()->webSite()->type('certification')->setData($data)->setMethodName('edit')->ready();
	}
}
