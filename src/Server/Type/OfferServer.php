<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class OfferServer
{
	/**
	 * @param $params
	 * @return mixed
	 */
  public function new($params) {
	  Api::post('offer',$params);
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
