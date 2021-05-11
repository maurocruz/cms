<?php
namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Cms\View\ViewInterface;
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
        $this->setOfferedBy($data);
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
}
