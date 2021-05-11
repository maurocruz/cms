<?php
namespace Plinct\Cms\View\Types\Product;

use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Tool\ArrayTool;

class ProductView extends ProductWidget implements ViewInterface {

    private function navbarProduct($value = null) {
        $list = [ "/admin/product" => _("View all"), "/admin/product/new" => _("Add new") ];
        $title = _("Product");
        $level = 2;
        $append = self::searchPopupList("Product");
        $this->content['navbar'][] = self::navbar($title, $list, $level, $append);
        if ($value) {
            $this->content['navbar'][] = self::navbar($value['name'], [], 3);
        }
    }

    public function index(array $data): array {
        self::navbarProduct();
        $additionalColumns = [
            "additionalType" => _("Additional type"),
            "availability" => _("Availability")
        ];
        $this->content['main'][] = self::listAll($data, "Product", _("List of products"), $additionalColumns);
        return $this->content;
    }

    public function new($data = null): array {
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
        $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        self::navbarProduct($value);
        // FORM
        $this->content['main'][] = self::divBox($value['name'], "product", [ parent::formProduct("edit", $value) ]);
        // IMAGES
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("product", $id, $value['image']) ]);
        return $this->content;
    }

    public function newWithPropertyOf($data = null): array {
        $this->content['main'][] = self::divBox(sprintf(_("Add new product for %s"), $data['manufacturer']['name']), "product", [ parent::formProduct("new", $data) ]);
        return $this->content;
    }

    public function indexWithPropertyOf($value) {
        $rowsColumns = [
            "name" => _("Name"),
            "category" => _("Category"),
            "additionalType" => _("Additional type"),
            "dateCreated" => _("Date created"),
            "dateModified" => _("Date modified")
        ];
        $this->content['main'][] = HtmlPiecesTrait::indexWithSubclass($value['name'], "products", $rowsColumns, $value['products']['itemListElement']);
        return $this->content;
    }

    public function editWithPropertyOf($value): array {
        $idProduct = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        // FORM EDIT PRODUCT
        $content[] = self::divBox2(_("Edit"), [ parent::formProduct("edit", $value) ]);
        // OFFERS
        $content[] = self::divBoxExpanding(_("Offer"), "offer", [ (new OfferView())->edit($value) ]);
        // IMAGES
        $content[] = self::divBoxExpanding(_("Images"), "imageObject", [ (new ImageObjectView())->getForm("product", $idProduct, $value['image']) ]);
        return $content;
    }
}
