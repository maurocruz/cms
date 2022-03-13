<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Offer;

use Plinct\Cms\WebSite\Fragment\Fragment;
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

        $form = Fragment::form(['class'=>'formPadrao form-offer']);
        $form->action("/admin/offer/$case")->method('post');
        // HIDDENS
        if ($this->tableHasPart && $this->idHasPart) {
            $form->input("itemOffered", (string)$this->idHasPart, "hidden");
            $form->input("itemOfferedType", $this->tableHasPart, "hidden");
        } else {
            $form->fieldset($form->chooseType("itemOffered", ["service", "product"], $value['itemOffered'], "name", ["style" => "display: flex;"]), _("Item offered"), ["style" => "width: 100%;"]);
        }
        if ($value) {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $form->input("id", $id, "hidden");
        }
        // PRICE
        $form->fieldsetWithInput("price", $value['price'] ?? null, _("Price"), "number", null, [ "min" => 0, "step" => "any" ]);
        // PRICE CURRENCY
        $form->fieldsetWithInput("priceCurrency", $value['priceCurrency'] ?? "R$",_("Price currency"), "text", null, [ "maxlength" => "2" ]);
        // AVAILABILITY
        $form->fieldsetWithSelect("availability", $value['availability'] ?? null, [
            "Discontinued" => _("Discontinued"),
            "InStock" => _("In stock"),
            "InStoreOnly" => _("In store only"),
            "LimitedAvailability" => _("Limited availability"),
            "OnlineOnly" => _("Online only"),
            "OutOfStock" => _("Out of stock"),
            "PreOrder" => _("Pre order"),
            "PreSale" => _("Pre sale"),
            "SoldOut" => _("Sould out")
        ], _("Availability"));
        // VALID THROUGH
        $form->fieldsetWithInput("validThrough", $value['validThrough'] ?? null, _("Valid through"));
        // ELEGIBLE QUANTITY
        $form->fieldsetWithInput("elegibleQuantity", $value['elegibleQuantity'] ?? null, _("Elegible quantity"));
        // ELEGIBLE DURATION
        $form->fieldsetWithInput("elegibleDuration", $value['elegibleDuration'] ?? null, _("Elegible duration"));
        // SUBMIT
        $form->submitButtonSend();
        if ($value) $form->submitButtonDelete("/admin/offer/erase");
        // READY
        return $form->ready();
    }
}
