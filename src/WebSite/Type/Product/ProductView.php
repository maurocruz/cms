<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Product;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\Intangible\Offer\OfferView;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Organization\OrganizationView;
use Plinct\Tool\ArrayTool;

class ProductView extends ProductAbstract
{
  /**
   * @param string|null $title
   */
  private function navbarProduct(string $title = null)
  {
    CmsFactory::webSite()->navbar(_("Product"), [
      "/admin/$this->manufacturerType/product?id=$this->manufacturer" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/$this->manufacturerType/product?id=$this->manufacturer&action=new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 4, ['table'=>'product'] );

    if ($title) {
      CmsFactory::webSite()->navbar($title, [], 5);
    }
  }

  /**
   * @throws Exception
   */
  public function edit($data)
  {
    $value = $data[0];
    if ($value['manufacturerType'] == 'organization') {
      $organization = $value['manufacturer'];
      $organization['action'] = 'edit';
      $organization['product'] = $value;
      (new OrganizationView())->product($organization);
    }
  }

  /**
   * @param null $value
   */
  public function newWithPartOf($value = null)
  {
    $this->manufacturer = ArrayTool::searchByValue($value['identifier'],'id','value');
    $this->manufacturerType = strtolower($value['@type']);

    $this->navbarProduct(_("Add new"));

    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(parent::formProduct()));
  }

  /**
   * @param $value
   */
  public function indexWithPartOf($value)
  {
    if (isset($value['products']['error']) || (isset($value['products']['status']) && $value['products']['status'] == 'error')) {
      $message = $value['products']['error']['message'] ?? $value['products']['message'] ?? "error";
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->error()->installSqlTable('product', $message));

    } else {
      $this->manufacturer = ArrayTool::searchByValue($value['identifier'], 'id', 'value');
      $this->manufacturerType = strtolower($value['@type']);
      $itemListElement = $value['products']['itemListElement'];

      $this->navbarProduct();

      $listTable = CmsFactory::response()->fragment()->listTable();
      // CAPTION
      $listTable->caption(sprintf(_("List of %s"), _("Products")));
      // LABELS
      $listTable->labels(_('Name'), _('Category'), _("Date modified"));
      // ROWS
      $listTable->rows($itemListElement, ['name', 'category', 'dateModified']);
      // BUTTONS
      $listTable->setEditButton("/admin/$this->manufacturerType/product?id=$this->manufacturer&item=");
      // READY
      CmsFactory::webSite()->addMain($listTable->ready());
    }
  }

  /**
   * @throws Exception
   */
  public function editWithPartOf($value)
  {
    $this->manufacturer = ArrayTool::searchByValue($value['identifier'],'id','value');
    $this->manufacturerType = strtolower($value['@type']);

    $product = $value['product'];
    $this->id = ArrayTool::searchByValue($product['identifier'],'id','value');

    $this->navbarProduct($product['name']);

    // FORM EDIT PRODUCT
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formProduct('edit',$product)));
    // OFFERS
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox( _("Offer"), (new OfferView())->editWithPartOf($product) ));
    // IMAGES
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox( _("Images"), (new ImageObjectView())->getForm("product", $this->id, $product['image']) ));
  }
}
