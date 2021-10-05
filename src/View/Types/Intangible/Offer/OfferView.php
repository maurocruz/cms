<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\View;
use Plinct\Tool\ArrayTool;

class OfferView extends OfferWidget
{
    /**
     *
     */
    private function navbarOffer()
    {
        View::navbar(_("Offer"), [
            "/admin/offer" => Fragment::icon()->home(),
            "/admin/offer/new" => Fragment::icon()->plus()
        ]);
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarOffer();

        View::main(self::listAll($data, "offer", null, [ "price" => _("Price"), "validThrough" => _("Valid through"), "itemOffered:name" => _("Item offered"), "itemOfferedType" => _("Item offered type") ]));
    }

    /**
     * @param array $data
     * @return array
     */
    public function editWithPartOf(array $data): array
    {
        $this->setOfferedBy($data);

        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = (int)ArrayTool::searchByValue($data['identifier'], "id")['value'];

        // NEW OFFER
        $content[] = Fragment::box()->expandingBox(sprintf(_("Add new %s"), _("offer")), parent::formOffer());

        if ($data['offers'] === null) {
            $content[] = Fragment::miscellaneous()->message(_("No offers found"));

        } else {
            foreach ($data['offers'] as $key => $value) {
                $number = $key + 1;
                $content[] = Fragment::box()->simpleBox(self::formOffer($value), _("Offer")." #$number");
            }
        }

        return $content;
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'], "id")['value'];
        View::main(self::divBoxExpanding(sprintf(_("Add new %s"), _("offer")), "offer", [ parent::formOffer() ]));
    }

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $data
     * @return array
     */
    public function getForm($tableHasPart, $idHasPart, $data): array
    {
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
