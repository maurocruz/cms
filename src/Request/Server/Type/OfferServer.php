<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class OfferServer
{
	/**
	 * @param array $params
	 * @return mixed
	 */
  public function new(array $params)
  {
	  CmsFactory::request()->api()->post('offer',$params)->ready();
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

	public function erase(array $params): string
	{
		CmsFactory::request()->api()->delete('offer',['idoffer'=>$params['idoffer']])->ready();
		return filter_input(INPUT_SERVER, 'HTTP_REFERER');
	}
}
