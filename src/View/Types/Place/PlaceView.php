<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Place;

use Exception;
use Plinct\Cms\View\Fragment\FormFragment;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\PostalAddressView;
use Plinct\Cms\View\Types\TypeViewInterface;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\OpenStreetMap;

class PlaceView implements TypeViewInterface
{
    /**
     * @var array
     */
    private array $content;
    /**
     * @var int
     */
    protected int $placeId;

    use FormElementsTrait;

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
        $listTable = new ListTable();
        $listTable->caption(_("Places"));
        $listTable->labels('id',_("Name"),_("AdditionalType"), _("Date modified"));
        $listTable->rows($data['itemListElement'],['idplace','name','additionalType','dateModified']);
        $listTable->setEditButton('/admin/place/edit/');
        MainView::content($listTable->ready());
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarPlace();
        MainView::content(self::divBox(_("Add new"), "Place", [ self::formPlace(null, null) ]));
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
        //place
        $place[] = self::formPlace(null, null, 'edit', $value);
        // address
        $place[] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("Place", $this->placeId, $value['address']) ]);
        // images
        $place[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("place", $this->placeId, $value['image']) ]);
        // append
        MainView::content(self::divBox($value['name'], 'place', $place));
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
        $latitude = isset($value['latitude']) ? (float)$value['latitude'] : null;
        $longitude = isset($value['longitude']) ? (float)$value['longitude'] : null;

        $content[] = $tableHasPart ? [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $tableHasPart ]] : null;        
        $content[] = $idHasPart ? [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $idHasPart ]] :  null;
        $content[] = $case == "edit" ? ["tag" => "input", "attributes" => ["name" => "id", "type" => "hidden", "value" => $this->placeId]]: null;

        if ($case == "new" && $tableHasPart && $idHasPart) {
            $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => "place" ] ];
        } else {
            // name
            $content[] = self::fieldsetWithInput(_("Place"), "name", $value['name'] ?? null);
            // ADDITIONAL TYPE
            $content[] = self::fieldset(FormFragment::selectAdditionalType('place',$value['additionalType'] ?? null), _("Additional type"));
            // Geo
            $content[] = self::fieldsetWithInput(_("Latitude"), "latitude", $latitude ?? null);
            $content[] = self::fieldsetWithInput(_("Longitude"), "longitude", $longitude ?? null);
            // elevation
            $content[] = self::fieldsetWithInput(_("Elevation (meters)"), "elevation", $value['elevation'] ?? null);
            // description
            $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null, 100);
            // disambiguating description
            $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
            // submit
            $content[] = self::submitButtonSend();
            if ($case == "edit") {
                $action = $tableHasPart ? "/admin/place/deleteRelationship" : "/admin/place/erase";
                $content[] = self::submitButtonDelete($action);
            }
            // map
            if ($latitude && $longitude) {
                $content[] = (new OpenStreetMap($latitude, $longitude))->attributes(['class'=>'form-place-map','width' => '100%', "height" => "300px"])->embedInIframe();
            }
        }
        return  [ "tag" => "form", "attributes" => [ "id" => "form-place-$case", "name" => "place-form-".$case, "class" => "formPadrao form-place", "method" => "post", "action" => "/admin/place/".$case ], "content" => $content ];
    }
}
