<?php
namespace Plinct\Cms\Controller\Type\Event;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class EventController implements TypeControllerInterface
{

	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('event')->ready();
	}

	public function new(array $params): bool
	{
		return false;
	}
	/**
   * @param array $params
   * @return bool
	 */
	public function edit(array $params): bool
	{
		$idevent = $params['idevent'];
		$data = CmsFactory::model()->type('event')->get(['idevent'=>$idevent,'properties'=>'location']);
	  return CmsFactory::view()->webSite()->type('event')->setData($data)->setMethodName('edit')->ready();
	}
}
