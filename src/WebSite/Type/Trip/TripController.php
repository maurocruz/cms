<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Plinct\Cms\Server\Api;

class TripController
{
  /**
   * @param $params
   * @return array
   */
  public function index($params = null): array
  {
		if (isset($params['provider'])) {
			$dataProvider = Api::get('organization',['idorganization'=>$params['provider']]);
			$data = $dataProvider[0];

			$params2 = ['format'=>'ItemList', 'provider'=>$params['provider'], 'orderBy'=>'dateModified desc'];
			$params3 = $params ?  array_merge($params2, $params) : $params2;
			$dataTrip = Api::get('trip',$params3);
			$dataTrip['name'] = 'List of provider trips';
			$data['trips'] = $dataTrip;

		} else {
			$params2 = ['format'=>'ItemList', 'fields'=>'distinct(provider)', 'groupBy'=>'provider', 'properties'=>'provider'];
			$params3 = $params ?  array_merge($params2, $params) : $params2;
			$data = Api::get('trip',$params3);
			$data['name'] = 'List of travel providers';
		}

    return $data;
  }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $id = $params['idtrip'] ?? null;
    return Api::get('trip',['idtrip'=>$id,'properties'=>'*,provider,image,identifier,subTrip']);
  }

  /**
   * @param $params
   * @return ?array
   */
  public function new($params = null): ?array
  {
		$data = null;
    $provider = $params['provider'] ?? null;
		if ($provider) {
			$dataProvider = Api::get('organization',['idorganization'=>$provider,'properties'=>'name']);
			$data[]['provider'] = $dataProvider[0];
		}
    return $data;
  }
}
