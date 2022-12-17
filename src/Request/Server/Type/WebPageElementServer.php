<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class WebPageElementServer
{
  /**
   * @param $params
   * @return mixed
   */
  public function new($params)
  {
    CmsFactory::request()->api()->post("webPageElement", $params)->ready();
    CmsFactory::request()->api()->put("webPage", ['idwebPage'=>$params['idHasPart'], 'dateModified'=>date('Y-m-d H:i:s') ])->ready();
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  /**
   * @param $params
   * @return mixed
   */
  public function edit($params)
  {
    $idHasPart = $params['idHasPart'];
		CmsFactory::request()->server()->api()->put('webPage',['idwebPage'=>$idHasPart, 'dateModified'=>date('Y-m-d H:i:s') ])->ready();
    unset($params['idHasPart']);
		return $params;
  }

  /**
   * @param $params
   * @return mixed
   */
  public function erase($params)
  {
    CmsFactory::request()->api()->delete('webPageElement', ['idwebPageElement'=>$params['idwebPageElement'] ?? $params['id']])->ready();
    CmsFactory::request()->api()->put("webPage", ['idwebPage'=>$params['idHasPart'], 'dateModified'=>date('Y-m-d H:i:s') ])->ready();
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
