<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class EventServer
{
	public function erase(array $params): string
  {
		if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['idIsPartOf'])) {
			Api::delete('event',$params);
			return filter_input(INPUT_SERVER, 'HTTP_REFERER');

		} else {
			Api::delete("event", ['idevent' => $params['id']]);
			return "/admin/event";
		}
  }
}
