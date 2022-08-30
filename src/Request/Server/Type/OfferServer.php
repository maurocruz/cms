<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class OfferServer
{
	/**
	 * @param $params
	 * @return mixed
	 */
  public function new($params) {
	  CmsFactory::request()->api()->post('offer',$params);
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
