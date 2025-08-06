<?php
namespace Plinct\Cms\Controller\Request\Server;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Enclave\Enclave;

class Server
{
  /**
   * @param $type
   */
  public function createSqlTable($type): void
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
		if (method_exists(CmsFactory::model()->type($type), $action)) {
			CmsFactory::model()->type($type)->$action($params);
		}
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  public static function enclave(): Enclave
  {
    return new Enclave();
  }
}
