<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\ServerAbstract;

class OrderServer
{
  /**
   * @param $params
   * @return string
   */
  public function new($params): string
  {
    $seller = $params['seller'];
    $sellerType = $params['sellerType'];
    // insert new order
    $data = CmsFactory::request()->api()->post('order', $params)->ready();
    $id = $data['idorder'];
    // REGISTER HISTORY IN ORDER REFERENCE
    $history = new HistoryServer('order', $id);
    $history->setSummary("Created new order");
    $history->register("CREATED");
    // RESPONSE
    return "/admin/" . lcfirst($sellerType) . "/order?id=$seller&item=$id";
  }

  /**
   * @param $params
   * @return mixed|void
   */
  public function edit($params)
  {
    // HISTORY
    $dataOld = CmsFactory::request()->api()->get('order', [ "idorder" => $params['idorder'] ])->ready();
    $history = new HistoryServer('order', $params['idorder']);
    $history->setSummaryByDifference($params, $dataOld[0]);
    $history->register("UPDATE")->ready();
    // RESPONSE
    return $params;
  }

  /**
   * @param $params
   * @return string
   */
  public function erase($params): string
  {
    $idorder = $params['id'] ?? $params['idIsPartOf'] ?? $params['idorder'];
    CmsFactory::request()->api()->delete('order', [ "idorder" => $idorder ])->ready();

    $tableHasPart = $params['tableHasPart'] ?? lcfirst($params['sellerType']);
    $idHasPart = $params['idHasPart'] ?? $params['seller'];

    return "/admin/$tableHasPart/order?id=$idHasPart";
  }
}
