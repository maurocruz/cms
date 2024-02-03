<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Person;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\WebSite\Type\Intangible\PostalAddressView;
use Plinct\Cms\WebSite\Type\Intangible\Service\ServiceView;
use Plinct\Cms\WebSite\Type\Product\ProductView;
use Plinct\Tool\ArrayTool;

class PersonView extends PersonViewAbstract
{
  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarPerson();
		CmsFactory::webSite()->addMain("
			<div
				class='plinct-shell'
				data-type='person'
				data-apihost='".App::getApiHost()."'
				data-userToken='".CmsFactory::request()->user()->userLogged()->getToken()."'
				data-columnsTable='{\"edit\":\"Edit\",\"idperson\":\"ID\",\"name\":\"Nome\",\"dateModified\":\"Modificação\"}'
			></div>");
    /*$list = CmsFactory::response()->fragment()->listTable();
    $list->caption( _("List of persons"));
    $list->labels(_('Name'), _('Date modified'));
    $list->rows($data['itemListElement'],['name','dateModified']);
    $list->setEditButton('/admin/person?id=');
    CmsFactory::webSite()->addMain($list->ready());*/
  }

  /**
   * @param
   */
  public function new() {
      $this->navbarPerson();
      CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->simpleBox(self::formPerson(),_("Add new"))
      );
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data) {
    if (!empty($data)) {
      $value = $data[0];

      $this->id = $value['idperson'];
      $this->setName($value['name']);

      // NAVBAR
      $this->navbarPersonEdit();

      // FORM
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formPerson('edit', $value), _("Edit person")));
      // CONTACT POINT
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('person', $this->id, $value['contactPoint'])));
      // ADDRESS
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Postal address"), (new PostalAddressView())->getForm("person", $this->id, $value['address'])));
      // IMAGE
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Image"), (new ImageObjectView())->getForm("Person", $this->id, $value['image'])));

    } else {
      $this->navbarPerson();
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->noContent(_("Person is not exists!")));
    }
  }

  /**
   * @param $value
   */
  public function service($value)
  {
    if ($value['@type'] == 'Service') {
      $this->id = ArrayTool::searchByValue($value['provider']['identifier'], 'id', 'value');
      $this->name = $value['provider']['name'];

      // NAVBAR
      $this->navbarPersonEdit();

      $service = new ServiceView();
      $service->editWithPartOf($value);

    } else {
      $this->id = ArrayTool::searchByValue($value['identifier'], 'id', 'value');
      $this->name = $value['name'];

      // NAVBAR
      $this->navbarPersonEdit();

      $service = new ServiceView();

      if (isset($value['action']) && $value['action'] ==  "new") {
        $service->newWithPartOf($value);
      } else {
        $service->listServices($value);
      }
    }
  }

  public function product($value)
  {
    $action = $value['action'] ?? null;
    $this->name = $value['name'];
    $this->id = ArrayTool::searchByValue($value['identifier'],'id','value');

    // NAVBAR
    $this->navbarPersonEdit();

    // MAIN
    $product = new ProductView();
    if ($action == 'new') {
      $product->newWithPartOf($value);
    } else {
      $product->indexWithPartOf($value);
    }
  }
}
