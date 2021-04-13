<?php
namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Cms\View\Html\Page\ViewInterface;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class OfferView extends OfferWidget implements ViewInterface {

    private function navbarOffer() {
        $this->content['navbar'][] = navbarTrait::navbar(_("Offer"), [
            "/admin/offer" => _("List all"),
            "/admin/offer/new" => _("Add new")
        ]);
    }

    public function index(array $data): array {
        $this->navbarOffer();
        $this->content['main'][] = self::listAll($data, "offer", null, [ "price" => _("Price"), "validThrough" => _("Valid through"), "itemOffered:name" => _("Item offered"), "itemOfferedType" => _("Item offered type") ]);
        return $this->content;
    }

    public function edit(array $data): array {
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'], "id")['value'];
        // NEW OFFER
        $this->content['main'][] = self::divBoxExpanding(sprintf(_("Add new %s"), _("offer")), "offer", [ parent::formOffer() ]);
        if ($data['offers'] === null) {
            $this->content['main'][] = self::noContent("No offers found");
        } else {
            foreach ($data['offers'] as $key => $value) {
                $number = $key + 1;
                $this->content['main'][] = self::divBox(_("Offer")." #$number", "offer", [self::formOffer($value)]);
            }
        }
        return $this->content;
    }

    public function new($data = null): array {
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'], "id")['value'];
        $this->content['main'][] = self::divBoxExpanding(sprintf(_("Add new %s"), _("offer")), "offer", [ parent::formOffer() ]);
        return $this->content;
    }

    public function getForm($tableHasPart, $idHasPart, $data): array {
        $content = null;
        $this->tableHasPart = $tableHasPart;
        $this->idHasPart = $idHasPart;
        if ($data) {
            foreach ($data as $value) {
                $content[] = self::formOffer($value);
            }
        } else {
            $content[] = ["New:", self::formOffer()];
        }
        return $content;
    }

    /*private static function formOffer($value = null): array {
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
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
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
    }*/
}
