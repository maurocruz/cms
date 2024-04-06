<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\Intangible\Service\Service;
use Plinct\Cms\View\WebSite\Type\Intangible\Order\Order;
use Plinct\Cms\View\WebSite\Type\Product\Product;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Organization extends OrganizationAbstract implements TypeInterface
{
	/**
	 * @param ?array $value
	 */
	public function index(?array $value): void
	{
		$this->navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('organization')->ready()
		);
	}
  /**
   * @param array|null $value
   * @param
   */
  public function new(?array $value)
  {
    // NAVBAR
    parent::navbarNew();
    //
    CmsFactory::View()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox( self::formOrganization(), _("Add organization"))
    );
  }
  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data)
  {
	  // NAVBAR
	  parent::navbarIndex();
		if (!empty($data)) {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('organization', $value);
			$this->idorganization = $typeBuilder->getId();
			$this->name = $value['name'];
			$idthing = $typeBuilder->getPropertyValue('idthing');
			// NAVBAR
			parent::navbarEdit();
			// ORGANIZATION
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Organization"), self::formOrganization('edit', $value), true)
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('organization', $this->idorganization, $value['contactPoint'] ?? null))
			);
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf($idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Organization is not exists!")));
		}
  }
  /**
   * @throws Exception
   */
  public function service($value)
  {
    $action = $value['action'] ?? filter_input(INPUT_GET, 'action');
    $item = filter_input(INPUT_GET, 'item');
    parent::setValues($value);
    // NAVBAR
    parent::navbarEdit();
    $service = new ServiceView();
    if ($action == 'new') {
      $service->newWithPartOf($value);
    } elseif ($action == 'edit' || $item) {
      $service->editWithPartOf($value);
    } else {
      $service->indexWithPartOf($value);
    }
  }
  /**
   * @param $value
   * @throws Exception
   */
  public function product($value)
  {
    $action = $value['action'] ?? filter_input(INPUT_GET, 'action');
    parent::setValues($value);
    // NAVBAR
    parent::navbarEdit();
    // MAIN
    $product = new ProductView();
    if ($action == 'new') {
      $product->newWithPartOf($value);
    } elseif($action == 'edit') {
      $product->editWithPartOf($value);
    } else {
      $product->indexWithPartOf($value);
    }
  }
  /**
   * ORDER
   * @param $value
   */
  public function order($value)
  {
    $action = $value['action'] ?? filter_input(INPUT_GET, 'action');
    $item = filter_input(INPUT_GET, 'item');
    // NAVBAR ORGANIZATION
    if ($value['@type'] == "Organization") {
      parent::setValues($value);
    } else {
      parent::setValues($value['seller']);
    }
    // NAVBAR
    parent::navbarEdit();
    // MAIN
    $order = new OrderView();
    if ($action == "payment") {
      $order->payment($value);
    } elseif ($action == "expired") {
      $order->expired($value);
    } elseif ($action == 'new') {
      $order->newWithPartOf($value);
    } elseif ($action == 'edit' || $item) {
      $order->editWithPartOf($value);
    } else {
      $order->indexWithPartOf($value);
    }
  }
}
