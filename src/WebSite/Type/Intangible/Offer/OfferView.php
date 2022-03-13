<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Offer;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
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

        $table = Fragment::listTable();
        $table->caption(sprintf(_("List of %s"), _("offers")));
        $table->labels(_("Price"), _("Valid through"), _("Item offered"), _("Item offered type"));
        $table->rows($data['itemListElement'], ['price','validThrough','itemOffered:name','itemOfferedType']);
        View::main($table->ready());
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
        View::main(Fragment::box()->expandingBox(sprintf(_("Add new %s"), _("offer")), parent::formOffer()));
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
