<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class OfferView implements ViewInterface
{
    private $content = [];

    use FormElementsTrait;

    private function navbarOffer($title = null, $list = [])
    {
        $this->content['navbar'][] = navbarTrait::navbar(_("Offer"), [
            "/admin/offer" => _("List all"),
            "/admin/offer/new" => _("Add new")
        ], 2);
    }

    public function index(array $data): array
    {
        $this->navbarOffer();

        $this->content['main'][] = self::listAll($data, "offer");

        return $this->content;
    }

    public function edit(array $data): array
    {
        return [];
    }

    public function new($data = null): array
    {
        $this->navbarOffer();

        //$this->content['main'][] = self::divBox(_("Add new"), "offer", [ self::formOffer() ]);

        return $this->content;
    }

    public function getForm($tableHasPart, $idHasPart, $value)
    {
        if ($value) {
            return self::formOffer($tableHasPart, $idHasPart,"edit", $value);
        } else {
            return self::formOffer($tableHasPart, $idHasPart);
        }
    }

    private function formOffer($tableHasPart, $idHasPart, $case = "new", $value = null)
    {
        $content[] = $case == "new" ? self::input('tableHasPart', "hidden", $tableHasPart) : null;
        $content[] = $case == "new" ? self::input('idHasPart', "hidden", $idHasPart) : null;

        if ($case == "edit") {
            $id = PropertyValue::extractValue($value['identifier'], 'id');
            $content[] = self::input("id", "hidden", $id);
        }

        $content[] = self::fieldsetWithInput(_("Price currency"), "priceCurrency", $value['priceCurrency'] ?? "R$", [ "style" => "width: 120px;"]);

        $content[] = self::fieldsetWithInput(_("Price"), "price", $value['price'], [ "style" => "width: 120px;" ], "number", [ "min" => 1, "step" => "any" ]);

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
        $content[] = self::fieldsetWithInput(_("Valid through"), "validThrough", $value['validThrough']);


        $content[] = self::submitButtonSend();

        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/offer/erase") : null;

        return self::form("/admin/offer/$case", $content);
    }
}