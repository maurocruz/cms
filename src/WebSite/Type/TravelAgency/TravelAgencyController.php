<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\TravelAgency;

use Plinct\Cms\CmsFactory;

class TravelAgencyController
{
  /**
   * @return array
   */
  public function index(): array
  {
    return CmsFactory::request()->api()->get('organization', ['format'=>'ItemList','additionalTypeLike'=>'TravelAgency','properties'=>'name'])->ready();
  }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $id = $params['idtravelAgency'] ?? null;
    $data = CmsFactory::request()->api()->get('organization',['id'=>$id,'properties'=>'name'])->ready();
    return $data[0];
  }

  /**
   * @param $params
   * @return void
   */
  public function new($params = null) {
      // TODO: Implement new() method.
  }
}
