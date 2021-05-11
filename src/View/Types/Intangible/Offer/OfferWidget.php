<?php
namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Tool\ArrayTool;

abstract class OfferWidget extends OfferAbstract {

    protected function formOffer($value = null): array {
        $case = $value ? "edit" : "new";
        if ($this->tableHasPart && $this->idHasPart) {
            //$content[] = $case == "new" ? self::input("idHasPart", "hidden", $this->idHasPart) : null;
            //$content[] = $case == "new" ? self::input("tableHasPart", "hidden", $this->tableHasPart) : null;
            $content[] = self::input("itemOffered", "hidden", $this->idHasPart);
            $content[] = self::input("itemOfferedType", "hidden", $this->tableHasPart);
        } else {
            $content[] = self::fieldset(self::chooseType("itemOffered", ["service", "product"], $value['itemOffered'], "name", ["style" => "display: flex;"]), _("Item offered"), ["style" => "width: 100%;"]);
        }
        $content[] = self::input("offeredBy", "hidden", ArrayTool::searchByValue($this->offeredBy['identifier'], "id")['value']);
        $content[] = self::input("offeredByType", "hidden", $this->offeredBy['@type']);
        if ($value) {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $content[] = self::input("id", "hidden", $id);
        }
        $content[] = self::fieldsetWithInput(_("Price"), "price", $value['price'] ?? null, [ "style" => "width: 120px;" ], "number", [ "min" => 0, "step" => "any" ]);
        $content[] = self::fieldsetWithInput(_("Price currency"), "priceCurrency", $value['priceCurrency'] ?? "R$", [ "style" => "width: 102px;"], "text", [ "maxlength" => "2" ]);
        $content[] = self::fieldsetWithSelect(_("Availability"), "availability", $value['availability'] ?? null, [
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
        $content[] = self::fieldsetWithInput(_("Valid through"), "validThrough", $value['validThrough'] ?? null, [ "style" => "width: 150px;" ]);
        // ELEGIBLE QUANTITY
        $content[] = self::fieldsetWithInput(_("Elegible quantity"), "elegibleQuantity", $value['elegibleQuantity'] ?? null, [ "style" => "width: 150px;" ]);
        // ELEGIBLE DURATION
        $content[] = self::fieldsetWithInput(_("Elegible duration"), "elegibleDuration", $value['elegibleDuration'] ?? null);
        $content[] = self::submitButtonSend();
        $content[] = $value ? self::submitButtonDelete("/admin/offer/erase") : null;
        return self::form("/admin/offer/$case", $content);
    }
}