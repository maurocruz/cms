<?php
namespace Plinct\Cms\Controller\Type\Organization;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class Organization implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('organization')->setMethodName('index')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('organization')->setMethodName('new')->ready();
	}

  /**
   * @param array $params
   * @return bool
   */
  public function edit(array $params): bool
  {
	  $data = CmsFactory::model()->api()->get("organization",['properties'=>'contactPoint,location,image'] + $params)->ready();
	  return CmsFactory::view()->webSite()->type('organization')->setData($data)->setMethodName('edit')->ready();
  }

  /**
   * SERVICE IS PART OF
   * @param array $params
   * @return bool
   */
  public function service(array $params): bool
  {
    $itemId = $params['item'] ?? null;
		$idorganization = $params['idorganization'] ?? null;
    if ($itemId) {
      $data = CmsFactory::model()->api()->get('service', [ "idservice" => $itemId, "properties" => "*,provider,offers" ])->ready();
    } else {
      $data = CmsFactory::model()->api()->get("organization",['idorganization'=>$idorganization,'makesOffer'=>'service'] + $params)->ready();
    }
		return CmsFactory::view()->webSite()->type('organization')->setData($data)->setMethodName('service')->ready();
  }

  /**
   *  PRODUCT IS PART OF
   * @param array $params
   * @return bool
   */
  public function product(array $params): bool
  {
    $idorganization = $params['idorganization'] ?? null;
    $itemId = $params['item'] ?? null;
    if ($itemId) {
      $data = CmsFactory::model()->api()->get('product', [ "idproduct" => $itemId, "properties" => "*,manufacturer,offers,image" ])->ready();
    } else {
	    $data = CmsFactory::model()->api()->get("organization",['idorganization'=>$idorganization,'makesOffer'=>'service'] + $params)->ready();
    }
	  return CmsFactory::view()->webSite()->type('organization')->setData($data)->setMethodName('product')->ready();
  }
}
