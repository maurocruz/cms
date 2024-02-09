<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Product;

use Plinct\Cms\CmsFactory;

class ProductController
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
    $params2 = [ "format" => "ItemList", "properties" => "availability,additionalType", "orderBy" => "availability, dateModified desc, position" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
    return CmsFactory::request()->api()->get("product", $params3)->ready();
  }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $params2 = array_merge($params, [ "properties" => "*,image,manufacturer,offers" ]);
    return CmsFactory::request()->api()->get("product", $params2)->ready();
  }

  /**
   * @return array
   */
  public function new(): array {
      return [];
  }
}
