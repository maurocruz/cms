<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Place;

use Exception;
use Plinct\Cms\Response\View\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\PostalAddressView;
use Plinct\Cms\WebSite\Type\TypeViewInterface;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\OpenStreetMap;

class PlaceView implements TypeViewInterface
{
    /**
     * @var int
     */
    protected int $placeId;

    /**
     *
     */
    public function navbarPlace(string $title = null)
    {
        View::navbar(_("Place"), [
            "/admin/place" => Fragment::icon()->home(),
            "/admin/place/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'place']);

        if ($title) {
            View::navbar($title, [], 3);
        }
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarPlace();

        $listTable = Fragment::listTable()
            ->caption(sprintf(_("List of %s"),_("places")))
            ->labels('id', _("Name"), _("AdditionalType"), _("Date modified"))
            ->rows($data['itemListElement'], ['idplace', 'name', 'additionalType', 'dateModified'])
            ->setEditButton('/admin/place/edit/');
        View::main($listTable->ready());
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarPlace();
        View::main(Fragment::box()->simpleBox(self::formPlace(null, null), _("Add new")));
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        $value = $data[0];
        $this->placeId = isset($value) ? (int)ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        // NAVBAR
        $this->navbarPlace($value['name']);
        // WARNINGS
        if (!$value['address']) View::main(Fragment::miscellaneous()->message(_("Is important that you define place 'address'"),['class'=>'warning']));
        //place
        $place[] = self::formPlace(null, null, 'edit', $value);
        // address
        $place[] = Fragment::box()->expandingBox(_("Postal address"), (new PostalAddressView())->getForm("Place", $this->placeId, $value['address']));
        // images
        $place[] = Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("place", $this->placeId, $value['image']));
        // append
        View::main(Fragment::box()->simpleBox($place,$value['name']));
    }

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param null $value
     * @return array
     */
    public function getForm($tableHasPart, $idHasPart, $value = null): array
    {
        $content[] = $value ? self::formPlace($tableHasPart, $idHasPart, 'edit', $value) :  self::formPlace($tableHasPart, $idHasPart);
        return $content;
    }

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param string $case
     * @param null $value
     * @return array
     */
    private function formPlace($tableHasPart, $idHasPart, string $case = "new", $value = null): array
    {
        $latitude = $value['latitude'] ?? null;
        $longitude = $value['longitude'] ?? null;

        $form = Fragment::form([ "id" => "form-place-$case", "name" => "place-form-".$case, "class" => "formPadrao form-place" ]);
        $form->action("/admin/place/$case")->method("post");
        // hiddens
        if ($tableHasPart) $form->input("tableHasPart", $tableHasPart, "hidden");
        if ($idHasPart) $form->input("idHasPart", $idHasPart, 'hidden');
        if ($case == "edit") $form->input("id", (string) $this->placeId, 'hidden');

        if ($case == "new" && $tableHasPart && $idHasPart) {
            $form->content([ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => "place" ] ]);
        } else {
            // name
            $form->fieldsetWithInput("name", $value['name'] ?? null, _("Place"));
            // ADDITIONAL TYPE
            $form->fieldset(Fragment::form()->selectAdditionalType('place',$value['additionalType'] ?? null), _("Additional type"));
            // Geo
            $form->fieldsetWithInput("latitude", $latitude, _("Latitude"))
                ->fieldsetWithInput("longitude", $longitude,_("Longitude"));
            // elevation
            $form->fieldsetWithInput("elevation", $value['elevation'] ?? null, _("Elevation (meters)"));
            // description
            $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
            // disambiguating description
            $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
            // submit
            $form->submitButtonSend();
            if ($case == "edit") {
                $formaction = $tableHasPart ? "/admin/place/deleteRelationship" : "/admin/place/erase";
                $form->submitButtonDelete($formaction);
            }
            // map
            if ($latitude && $longitude) {
                $form->content(
                    (new OpenStreetMap((float)$latitude, (float)$longitude))
                        ->attributes(['class'=>'form-place-map','width' => '100%', "height" => "300px"])
                        ->embedInIframe()
                );
            }
        }

        return $form->ready();
    }
}
