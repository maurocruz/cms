<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class OfferView implements ViewInterface
{
    private $content = [];

    private static $tableHasPart;
    private static $idHasPart;

    use FormElementsTrait;

    private function navbarOffer($title = null, $list = [])
    {
        $this->content['navbar'][] = navbarTrait::navbar(_("Offer"), [
            "/admin/offer" => _("List all"),
            "/admin/offer/new" => _("Add new")
        ]);
    }

    public function index(array $data): array
    {
        $this->navbarOffer();

        $this->content['main'][] = self::listAll($data, "offer", null, [ "price" => _("Price"), "validThrough" => _("Valid through"), "itemOffered:name" => _("Item offered"), "itemOfferedType" => _("Item offered type") ]);

        return $this->content;
    }

    public function edit(array $data): array
    {
        $this->navbarOffer();

        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            $value = $data[0];
            $this->content['main'][] = self::divBox(_("Offer"), "offer", [ self::formOffer($value) ]);
        }

        return $this->content;
    }

    public function new($data = null): array
    {
        $this->navbarOffer();

        $this->content['main'][] = self::divBox(_("Add new"), "offer", [ self::formOffer() ]);

        return $this->content;
    }

    public function getForm($tableHasPart, $idHasPart, $data): array
    {
        self::$tableHasPart = $tableHasPart;
        self::$idHasPart = $idHasPart;

        if ($data) {
            foreach ($data as $value) {
                $content[] = self::formOffer($value);
            }
        } else {
            $content[] = ["New:", self::formOffer()];
        }

        return $content;
    }

    public static function formChooseType($tableHasPart, $idHasPart, $idSeller, $n, $value = null): array
    {
        $content[] = self::input("tableHasPart", "hidden", $tableHasPart);
        $content[] = self::input("idHasPart", "hidden", $idHasPart);
        $content[] = $n.": ";
        $content[] = self::chooseType("itemOffered", "service,product", $value['itemOffered'], "name", [ "style" => "width: 70%; display: inline-flex;", "data-params" => "provider=$idSeller" ]);

        $content[] = $value['price'] ? self::input("price", "text", $value['priceCurrency']." ".number_format($value['price'],2,',','.'), [ "readonly", "style" => "width: auto; margin-left: 2px;" ]): null;

        $content[] = self::submitButtonSend([ "style" => "height: 30px; vertical-align: middle" ]);
        $content[] = $value ? self::submitButtonDelete("/admin/offer/erase", [ "style" =>"height: 30px; vertical-align: middle" ]) : null;

        $case = $tableHasPart == "order" ? "addInOrder" : "new";
        return self::form("/admin/offer/$case", $content);
    }

    private static function formOffer($value = null): array
    {
        $case = $value ? "edit" : "new";

        if (isset(self::$tableHasPart)) {
            $itemOffered = self::$idHasPart;
            $itemOfferedType = self::$tableHasPart;
            $content[] = self::input("tableHasPart", "hidden", $itemOfferedType);
            //$content[] = self::input("idHasPart", "hidden", $itemOffered);
            $content[] = self::input("itemOffered", "hidden", $itemOffered);
            $content[] = self::input("itemOfferedType", "hidden", $itemOfferedType);
        } else {
            $content[] = self::fieldset(self::chooseType("itemOffered", ["service", "product"], $value['itemOffered'], "name", ["style" => "display: flex;"]), _("Item offered"), ["style" => "width: 100%;"]);
        }

        if ($value) {
            $id = PropertyValue::extractValue($value['identifier'], 'id');
            $content[] = self::input("id", "hidden", $id);
        }


        $content[] = self::fieldsetWithInput(_("Price"), "price", $value['price'], [ "style" => "width: 120px;" ], "number", [ "min" => 0, "step" => "any" ]);

        $content[] = self::fieldsetWithInput(_("Price currency"), "priceCurrency", $value['priceCurrency'] ?? "R$", [ "style" => "width: 102px;"], "text", [ "maxlength" => "2" ]);

        $content[] = self::fieldsetWithSelect(_("Availability"), "availability", $value['availability'], [
            "Discontinued" => _("Discontinued"),
            "InStock" => _("In stock"),
            "InStoreOnly" => _("In store only"),
            "LimitedAvailability" => _("Limited availability"),
            "OnlineOnly" => _("Online only"),
            "OutOfStock" => _("Out of stock"),
            "PreOrder" => _("Pre order"),
            "PreSale" => _("Pre sale"),
            "SoldOut" => _("Sould out")
        ]);
        // VALID THROUGH
        $content[] = self::fieldsetWithInput(_("Valid through"), "validThrough", $value['validThrough'], [ "style" => "width: 150px;" ]);
        // ELEGIBLE QUANTITY
        $content[] = self::fieldsetWithInput(_("Elegible quantity"), "elegibleQuantity", $value['elegibleQuantity'], [ "style" => "width: 150px;" ]);
        // ELEGIBLE DURATION
        $content[] = self::fieldsetWithInput(_("Elegible duration"), "elegibleDuration", $value['elegibleDuration']);

        $content[] = self::submitButtonSend();

        $content[] = $value ? self::submitButtonDelete("/admin/offer/erase") : null;

        return self::form("/admin/offer/$case", $content);
    }
}