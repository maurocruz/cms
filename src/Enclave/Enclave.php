<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Enclave;

use Plinct\Cms\Controller\CmsFactory;

class Enclave
{
  /**
   * @param string $classNameSpace
   * @param array $queryParams
   * @return void
   */
  public function get(string $classNameSpace, array $queryParams)
  {
    if (class_exists($classNameSpace)) {
      $classObject = new $classNameSpace();
      if (method_exists($classObject, 'viewMain')) {
				$classObject->view($queryParams);
      }
    } else {
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->message(_("Enclave not found!")));
    }
  }

  public function post(string $classNameSpace, array $params): string
  {
		$returns = null;
    if (class_exists($classNameSpace)) {
      $classObject = new $classNameSpace();
      if (method_exists($classObject, 'post')) {
         $returns = $classObject->post($params);
      }
    }
    return $returns;
  }

  public function put(string $classNameSpace, array $params): string
  {
	  $returns = null;
    if (class_exists($classNameSpace)) {
      $classObject = new $classNameSpace();
      if (method_exists($classObject, 'put')) {
        $returns = $classObject->put($params);
      }
    }
    return $returns;
  }

  public function delete(string $classNameSpace, array $params): string
  {
	  $returns = null;
      if (class_exists($classNameSpace)) {
        $classObject = new $classNameSpace();
        if (method_exists($classObject, 'delete')) {
          $returns = $classObject->delete($params);
        }
      }
      return $returns;
  }
}
