<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server;

use Plinct\Cms\Controller\Enclave\Enclave;

class Server
{

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
		if (method_exists($this->api(), $action)) {
			$this->api()->$action($type, $params)->ready();
		}
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  public static function enclave(): Enclave
  {
    return new Enclave();
  }
}
