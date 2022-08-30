<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\Intangible\Service\ServiceView;
use Plinct\Cms\WebSite\Type\Intangible\Order\OrderView;
use Plinct\Cms\WebSite\Type\Product\ProductView;

class OrganizationView extends OrganizationAbstract
{
  /**
   * @param array $data
   */
  public function index(array $data)
  {
  // NAVBAR
  parent::navbarIndex();

  $listTable = CmsFactory::response()->fragment()->listTable()
    ->caption(_("List of organizations"))
    ->labels(_('Name'),_('Additional type'), _("Date modified"))
    ->rows($data['itemListElement'],['name','additionalType','dateModified'])
    ->setEditButton("/admin/organization/edit/");

  CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   * @param
   */
  public function new()
  {
    // NAVBAR
    parent::navbarNew();
    //
    CmsFactory::webSite()->addMain(
			CmsFactory::response()->fragment()->box()->simpleBox( self::formOrganization(), _("Add organization"))
    );
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data)
  {
	  // NAVBAR
	  parent::navbarIndex();

		// NOT Autohorization
		if (isset($data['status']) && $data['status'] == 'fail') {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->message()->warning('Cms warn: The user is do not authorized for this operation')
			);
		}
		// DATA EMPTY
		elseif (empty($data)) {
			CmsFactory::webSite()->addMain(
        CmsFactory::response()->message()->noContent(_("No item founded!")));
		}
		// VIEW
		else {
      $value = parent::setValues($data[0]);
      // NAVBAR
      parent::navbarEdit();
      // ORGANIZATION
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->simpleBox(
					self::formOrganization('edit', $value), sprintf(_("Edit %s"), _("organization"))
				)
      );
      // LOCATION
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->expandingBox(
					_("Place"),
					CmsFactory::response()->fragment()->form()->relationshipOneToOne("organization", $this->id, "location", "place", $value['location'])
				)
      );
      // CONTACT POINT
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->expandingBox(
					_("Contact point"),
					CmsFactory::webSite()->type('contactPoint')->intangible()->contactPoint()->getForm('organization', $this->id, $value['contactPoint'])
				)
      );
      // MEMBER
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->expandingBox(
					_("Persons"),
					CmsFactory::response()->fragment()->form()->relationshipOneToMany("organization", $this->id, "person", $value['member']))
      );
      // IMAGE
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->expandingBox(
					_("Images"),
					CmsFactory::webSite()->type('imageObject')->getForm("organization", $this->id, $value['image'])
				)
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
