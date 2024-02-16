<?php

declare(strict_types=1);

namespace Plinct\Cms\View\WebSite\Type\Intangible\Service;

use Plinct\Cms\Controller\CmsFactory;

class ServiceController
{
  /**
   * @return array
   */
  public function index(): array {
    $params2 = [ "format" => "ItemList", "properties" => "provider,offers" ];
    return CmsFactory::request()->api()->get("service", $params2)->ready();
  }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array {
    return CmsFactory::request()->api()->get("service", [ "idservice" => $params['idservice'], "properties" => "*,provider,offers", "limit" => "none" ])->ready();
  }

  /**
   * @return null
   */
  public function new() {
    return null;
  }

  /**
   * @param $params
   * @return array
   */
  public function order($params): array {
      $idservice = $params['idservice'];
      $params2 = [ "idservice" => $idservice ];
      $dataService = CmsFactory::request()->api()->get("service", $params2)->ready();
      $valueService = $dataService[0];
      $params3 = [ "format" => "ItemList", "orderedItem" => $idservice, "orderedItemType" => "service", "properties" => "*,seller", "orderBy" => "orderDate" ];
      $valueService['orders'] = CmsFactory::request()->api()->get("order", $params3)->ready();
      return $valueService;
  }

  public function provider($params): array {
    $idservice = $params['idservice'];
    $params2 = [ "format" => "ItemList", "provider" => $idservice, "properties" => "*,provider" ];
    return CmsFactory::request()->api()->get("service", $params2)->ready();
  }
}
