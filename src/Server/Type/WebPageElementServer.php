<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class WebPageElementServer
{
  /**
   * @param $params
   * @return mixed
   */
  public function new($params)
  {
    Api::post("webPageElement", $params);
    Api::put("webPage", ['idwebPage'=>$params['idHasPart'], 'dateModified'=>date('Y-m-d H:i:s') ]);
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  /**
   * @param $params
   * @return mixed
   */
  public function edit($params)
  {
    $idHasPart = $params['idHasPart'];
    unset($params['idHasPart']);
    Api::put("webPageElement", $params);
    Api::put("webPage", ['idwebPage'=>$idHasPart, 'dateModified'=>date('Y-m-d H:i:s') ]);
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  /**
   * @param $params
   * @return mixed
   */
  public function erase($params)
  {
    Api::delete('webPageElement', ['idwebPageElement'=>$params['idwebPageElement'] ?? $params['id']]);
    Api::put("webPage", ['idwebPage'=>$params['idHasPart'], 'dateModified'=>date('Y-m-d H:i:s') ]);
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
