<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class EventServer
{
	public function erase(array $params): string
  {
		if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['idIsPartOf'])) {
			CmsFactory::request()->api()->delete('event',$params);
			return filter_input(INPUT_SERVER, 'HTTP_REFERER');

		} else {
			CmsFactory::request()->api()->delete("event", ['idevent' => $params['id']]);
			return "/admin/event";
		}
  }
}
