<?php
namespace Plinct\Cms\Controller\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\ThingController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class EventController extends ThingController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'event')
	{
		$this->sitemapExtension = 'news';
		parent::__construct($type);
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('event')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('event')->setMethodName('new')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$idevent = $params['idevent'];
		$data = CmsFactory::model()->type('event')->get(['idevent'=>$idevent]);
	  return CmsFactory::view()->webSite()->type('event')->setData($data)->setMethodName('edit')->ready();
	}
}
