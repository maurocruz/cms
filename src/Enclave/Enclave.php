<?php
namespace Plinct\Cms\Enclave;
use Plinct\Cms\CmsFactory;

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
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message(_("Enclave not found!")));
    }
  }

	/**
	 * @param string $classNameSpace
	 * @param array $params
	 * @return string
	 */
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

	/**
	 * @param string $classNameSpace
	 * @param array $params
	 * @return string
	 */
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

	/**
	 * @param string $classNameSpace
	 * @param array $params
	 * @return string
	 */
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
