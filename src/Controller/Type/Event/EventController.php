<?php
namespace Plinct\Cms\Controller\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Cms\Helpers\Sitemap;

class EventController implements TypeControllerInterface
{
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
		return false;
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

	/**
	 * @throws Exception
	 */
	public function sitemap(): bool
	{
		$data = CmsFactory::model()->type('event')->get(['fields'=>'startDate,name,url,dateModified,dateCreated','orderBy' => 'startDate', 'ordering' => 'desc','limit'=>'none']);
		$dataSitemap = Sitemap::buildNewsSitemap($data);
		return CmsFactory::view()->webSite()->type('event')->setData($dataSitemap)->setMethodName('sitemap')->ready();
	}
}
