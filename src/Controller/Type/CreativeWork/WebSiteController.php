<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class WebSiteController implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('webSite')->setMethodName('index')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return false;
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
  public function edit(?array $params): bool
  {
    $data = CmsFactory::model()->api()->get('webSite',$params)->ready();
		return CmsFactory::view()->webSite()->type('webSite')->setMethodName('edit')->setData($data)->ready();
  }
}
