<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Enclave\Enclave;
use Plinct\Cms\Request\Api\Api;
use Plinct\Cms\Request\Server\Type\Type;

class Server
{
	/**
	 * @return Api
	 */
	public function api(): Api
	{
		return new Api();
	}

	/**
	 * @return Auth
	 */
	public function auth(): Auth
	{
		return new Auth();
	}

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @return string | array
	 */
  public function new(string $type, array $params)
  {
		// GET PARAMS
		$returns = CmsFactory::request()->server()->type($type)->setParams('new', $params)->getParams();
		if (is_string($returns)) {
			return $returns;
		} elseif (is_array($returns)) {
			// API
			$data = CmsFactory::request()->api()->post($type, $returns)->ready();
			if ((isset($data['status']) && $data['status'] == 'fail') || (isset($data['error']))) {
				if(isset($data['error'])) {
					$data = $data['error'];
					$data['status'] = 'error';
				}
				return $data;
			}
			// REDIRECT TO EDIT PAGE
			if (isset($data['id']) && !isset($params['tableHasPart'])) {
				return App::getURL() . dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
			}
		}
		return filter_input(INPUT_SERVER, 'HTTP_REFERER');

  }

  /**
   * @param $type
   * @param $params
   * @return string | array
   */
  public function edit($type, $params)
  {
	  $params = CmsFactory::request()->server()->type($type)->setParams('edit', $params)->getParams();
		if (is_string($params)) {
			return ['message'=>$params];
		}
		elseif (is_array($params)) {
			$data = CmsFactory::request()->api()->put($type, $params)->ready();
			if (isset($data['status']) && $data['status'] == 'fail') {
				return $data;
			}
		}
		return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

	/**
	 * @param string $type
	 * @param array $params
	 * @return string
	 */
  public function erase(string $type, array $params): string
  {
	  /*$classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";

	  if (class_exists($classTypeServer)) {
		  $objectType = new $classTypeServer();
		  if (method_exists($objectType,"erase")) {
			  return $objectType->erase($params);
		  }
	  }*/
	  $params = CmsFactory::request()->server()->type($type)->setParams('erase', $params)->getParams();

    if (is_string($params)) {
			return $params;
    } else {
	    $id = $params['id'.lcfirst($type)] ?? $params['id'] ?? $params['idIsPartOf'] ?? null;
			if ($id) {
				$newParams["id" . lcfirst($type)] = $id;
				if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
					$newParams['tableHasPart'] = $params['tableHasPart'];
					$newParams['idHasPart'] = $params['idHasPart'];
					$newParams['tableIsPartOf'] = lcfirst($type);
					$newParams['idIsPartOf'] = $id;
				}
				$response = CmsFactory::request()->api()->delete($type, $newParams)->ready();
			} else {
				$response = ['status'=>'fail','message'=>'No post id value for delete action'];
			}

	    // RESPONSE REDIRECT
	    if (isset($response['error'])) {
	      print_r([ "error" => [ "response" => $response ]]);
	        die("Error message: {$response['error']['message']}");
	    } else {
	      return isset($params['tableHasPart']) ? filter_input(INPUT_SERVER, 'HTTP_REFERER') : dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
	    }
		}
  }

  /**
   * @param $type
   */
  public function createSqlTable($type)
  {
    $classname = "Plinct\\Api\\Type\\".ucfirst($type);
    (new $classname())->createSqlTable($type);
  }

  /**
   * @param $type
   * @param $action
   * @param $params
   * @return string
   */
  public function request($type, $action, $params): string
  {
		$this->api()->$action($type,$params)->ready();
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  public static function enclave(): Enclave
  {
    return new Enclave();
  }
}
