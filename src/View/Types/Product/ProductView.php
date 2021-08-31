<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Product;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\View;
use Plinct\Tool\ArrayTool;

class ProductView extends ProductAbstract
{
    /**
     * @param string|null $title
     */
    private function navbarProduct(string $title = null)
    {
        View::navbar(_("Product"), [
            "/admin/$this->manufacturerType/product?id=$this->manufacturer" => Fragment::icon()->home(),
            "/admin/$this->manufacturerType/product?id=$this->manufacturer&action=new" => Fragment::icon()->plus()
        ], 4, ['table'=>'product'] );

        if ($title) {
            View::navbar($title, [], 5);
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

        View::main(Fragment::box()->simpleBox(parent::formProduct()));
    }

    /**
     * @param $value
     */
    public function indexWithPartOf($value)
    {
        $this->manufacturer = ArrayTool::searchByValue($value['identifier'],'id','value');
        $this->manufacturerType = strtolower($value['@type']);
        $itemListElement = $value['products']['itemListElement'];

        $this->navbarProduct();

        $listTable = Fragment::listTable();
        // CAPTION
        $listTable->caption(sprintf(_("List of %s"), _("Products")));
        // LABELS
        $listTable->labels(_('Name'), _('Category'),_("Date modified"));
        // ROWS
        $listTable->rows($itemListElement,['name','category','dateModified']);
        // BUTTONS
        $listTable->setEditButton("/admin/$this->manufacturerType/product?id=$this->manufacturer&item=");
        // READY
        View::main($listTable->ready());
    }

    /**
     * @throws Exception
     */
    public function editWithPartOf($value)
    {
        $this->manufacturer = ArrayTool::searchByValue($value['identifier'],'id','value');
        $this->manufacturerType = strtolower($value['@type']);

        $product = $value['product'][0];
        $this->id = ArrayTool::searchByValue($product['identifier'],'id','value');

        $this->navbarProduct($product['name']);

        // FORM EDIT PRODUCT
        View::main(Fragment::box()->simpleBox(self::formProduct('edit',$product)));

        // OFFERS
        View::main(Fragment::box()->expandingBox( _("Offer"), (new OfferView())->editWithPartOf($product) ));
        // IMAGES
        View::main(Fragment::box()->expandingBox( _("Images"), (new ImageObjectView())->getForm("product", (int)$this->id, $value['image']) ));
    }
}
