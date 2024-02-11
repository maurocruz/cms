<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type;

use Plinct\Cms\Controller\CmsFactory;

class View
{
	/**
	 * @param $type
	 * @param $methodName
	 * @param $data
	 * @param $params
	 * @return void
	 */
  public function view($type, $methodName, $data, $params)
  {
    $className = __NAMESPACE__ . "\\" . ucfirst($type) . "\\" . ucfirst($type) . "View";

    // ERROR
    $error = $data['error'] ?? null;
    if ($error) {
      switch ($error['code']) {
        case '42S02':
          CmsFactory::webSite()->addMain(
						CmsFactory::response()->fragment()->error()->installSqlTable($type, $error['message']));
          break;
        default:
          CmsFactory::webSite()->addMain("<p class='warning'>Message: {$error['message']}</p>");
          CmsFactory::webSite()->addMain("<p class='warning'>Code: {$error['code']}</p>");
          CmsFactory::webSite()->addMain("<p class='warning'>Query: {$error['query']}</p>");
      }
    }

    // VIEW
    elseif (class_exists($className)) {
      $object = new $className();
      if (method_exists($object,$methodName)) {
        $object->{$methodName}($data, $params);
	    } else {
        CmsFactory::webSite()->addMain("<p class='warning'>Method view not exists!</p>");
      }
    }

    // TYPE NOT FOUND
    else {
      CmsFactory::webSite()->addMain("<p class='warning'>$type type view not founded</p>");
    }
  }
}
