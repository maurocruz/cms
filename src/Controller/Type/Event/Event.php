<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type\Event;

use Plinct\Cms\CmsFactory;

class Event
{
	/**
   * @param array $params
   * @return bool
	 */
	public function edit(array $params): bool
	{
	  $params = array_merge($params, [ "properties" => "*,location,superEvent,subEvent" ]);
		$data = CmsFactory::model()->api()->get('event', $params)->ready();
	  return CmsFactory::view()->webSite()->type('event')->setData($data)->setMethodName('edit')->ready();
	}
}
