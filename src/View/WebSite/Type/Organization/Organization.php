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
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Organization extends OrganizationAbstract implements TypeInterface
{
  /**
   * @param ?array $value
   */
  public function index(?array $value)
  {
		$apiHost = App::getApiHost();
		// NAVBAR
	  parent::navbarIndex();
		// index
	  CmsFactory::view()->addMain("<div class='plinct-shell' data-type='organization' data-apihost='$apiHost'></div>");
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
		// NOT Autohorization
		if (isset($data['status']) && $data['status'] == 'fail') {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->message()->warning('Cms warn: The user is do not authorized for this operation')
			);
		}
		// DATA EMPTY
		elseif (empty($data)) {
			CmsFactory::view()->addMain(
        CmsFactory::view()->fragment()->message()->noContent(_("No item founded!")));
		}
		// VIEW
		else {
			$apiHost = App::getApiHost();
			$userToken = CmsFactory::controller()->user()->userLogged()->getToken();
      $value = parent::setValues($data[0]);
      // NAVBAR
      parent::navbarEdit();
			// THING
			CmsFactory::view()->addMain("<div class='plinct-shell' data-type='organization' data-idispartof='{$value['idorganization']}' data-apihost='$apiHost' data-usertoken='$userToken'></div>");
      // ORGANIZATION
      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Organization"), self::formOrganization('edit', $value))
      );
      // LOCATION
      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Place"), CmsFactory::view()->fragment()->form()->relationshipOneToOne("organization", $this->id, "location", "place", $value['location']))
      );
      // CONTACT POINT

      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('organization', $this->id, $value['contactPoint'] ?? null))
      );
      // MEMBER
      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Persons"), CmsFactory::view()->fragment()->form()->relationshipOneToMany("organization", $this->id, "person", $value['member'] ?? null)));
      // IMAGE
      CmsFactory::view()->addMain("<div class='plinct-shell' data-type='imageObject' data-tablehaspart='organization' data-idhaspart='{$value['idorganization']}'data-apihost='$apiHost' data-usertoken='$userToken'></div>"
      );
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
