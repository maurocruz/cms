<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Piece\FormElementsTrait;
use Plinct\Cms\View\Html\Piece\navbarTrait;

class ProductView implements ViewInterface
{
    private $content;

    use FormElementsTrait;
    Use navbarTrait;

    private function navbarProduct($value = null)
    {
        $list = [
            "/admin/product" => _("View all"),
            "/admin/product/new" => _("Add new")
        ];
        $title = _("Product");
        $level = 2;
        $append = self::searchPopupList("Product");

        $this->content['navbar'][] = self::navbar($title, $list, $level, $append);

        if ($value) {
            $this->content['navbar'][] = self::navbar($value['name'], [], 3);
        }
    }

    public function index(array $data): array
    {
        self::navbarProduct();

        $additionalColumns = [
            "additionalType" => _("Additional type"),
            "availability" => _("Availability")
        ];

        $this->content['main'][] = self::listAll($data, "Product", _("List of products"), $additionalColumns);

        return $this->content;
    }

    public function edit(array $data): array
    {
        $value = $data[0];
        $id = PropertyValue::extractValue($value['identifier'], "id");

        self::navbarProduct($value);

        // FORM
        $this->content['main'][] = self::divBox($value['name'], "product", [ self::form("edit", $value) ]);

        // IMAGES
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("product", $id, $value['image']) ]);

        return $this->content;
    }

    public function new($data = null): array
    {
        $this->navbarProduct();

        $this->content['main'][] = self::divBox(_("Add new"), "product", [ self::form() ]);

        return $this->content;
    }

    private function form($case = "new", $value = null)
    {
        // id
        $content[] = $case == "edit" ? self::input("id", "hidden", PropertyValue::extractValue($value['identifier'], "id")) : null;
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'], [ "style" => "width: calc(100% - 660px);"] );
        // additional type
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "width: 300px;"] );
        // category
        $content[] = self::fieldsetWithInput(_("Category"), "category", $value['category'], [ "style" => "width: 300px;"] );
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'], [ "style" => "width: 30px;"] );
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description']);
        // availability
        $content[] = self::radio(_("Availability"), "availability", $value['availability'], [ "InStock", "OutOfStock" ]);
        // created time
        $content[] = self::fieldsetWithInput(_("Date created"), "dateCreated", $value['dateCreated'], [ "style" => "width: 200px;"], "text", [ "disabled" ] );
        // update time
        $content[] = self::fieldsetWithInput(_("Date modified"), "dateModified", $value['dateModified'], [ "style" => "width: 200px;"], "text", [ "disabled" ] );
        // submit
        $content[] = self::submitButtonSend();

        return [ "tag" => "form", "attributes" => [ "action" => "/admin/product/$case", "method" => "post", "class" => "formPadrao"], "content" => $content ];
    }
}