<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Tool\ArrayTool;

abstract class OfferWidget extends OfferAbstract
{
    /**
     * @param null $value
     * @return array
     */
    protected function formOffer($value = null): array
    {
        $case = $value ? "edit" : "new";

        if ($this->tableHasPart && $this->idHasPart) {
            $content[] = self::input("itemOffered", "hidden", (string)$this->idHasPart);
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

        $content[] = self::fieldsetWithInput(_("Price"), "price", $value['price'] ?? null, null, "number", [ "min" => 0, "step" => "any" ]);
        $content[] = self::fieldsetWithInput(_("Price currency"), "priceCurrency", $value['priceCurrency'] ?? "R$", null, "text", [ "maxlength" => "2" ]);
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
        $content[] = self::fieldsetWithInput(_("Valid through"), "validThrough", $value['validThrough'] ?? null);
        // ELEGIBLE QUANTITY
        $content[] = self::fieldsetWithInput(_("Elegible quantity"), "elegibleQuantity", $value['elegibleQuantity'] ?? null);
        // ELEGIBLE DURATION
        $content[] = self::fieldsetWithInput(_("Elegible duration"), "elegibleDuration", $value['elegibleDuration'] ?? null);
        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $value ? self::submitButtonDelete("/admin/offer/erase") : null;

        return self::form("/admin/offer/$case", $content,['class'=>'formPadrao form-offer']);
    }
}
