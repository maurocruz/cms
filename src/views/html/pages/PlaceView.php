<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Piece\navbarTrait;
use Plinct\Cms\Views\Html\Piece\FormTrait;

class PlaceView
{
    private $content;

    protected $placeId;
    
    protected $placeName;

    use navbarTrait;
    use FormTrait;
    
    public function navbarPlace()
    {
        $title = _("Place");
        $list = [
            "/admin/place" => _("View all"),
            "/admin/place/new" => _("Add new place")
        ];
        $level = 2;
        $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "place", "data-searchfor" => "name" ] ];

        $this->content['navbar'][] = self::navbar($title, $list, $level, $search);
    }

    public function index(array $data): array
    {   
        $this->navbarPlace();
        
        $this->content['main'][] = self::listAll($data, "place", _("Places"), [ "dateModified" => "Date modified" ]);
        
        return $this->content;
    }
    
    public function new($data = null): array
    {
        $this->navbarPlace();

        $this->content['main'][] = self::divBox(_("Add new"), "Place", [ self::form(null, null) ]);
        
        return $this->content;
    }

    public function edit(array $data): array
    {
        $this->navbarPlace();

        $value = $data[0];
        
        $this->placeId = PropertyValue::extractValue($value['identifier'], "id");

        $this->placeName = $value['name'];

        //place
        $place[] = self::form(null, null, 'edit', $value);

        // address
        $place[] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("Place", $this->placeId, $value['address']) ]);

        // images
        $place[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("place", $this->placeId, $value['image']) ]);

        // append
        $this->content['main'][] = self::divBox($value['name'], 'place', $place);

        
        return $this->content;
    }
    
    public function getForm($tableHasPart, $idHasPart, $value = null) 
    {
        $content[] = $value ? self::form($tableHasPart, $idHasPart, 'edit', $value) :  self::form($tableHasPart, $idHasPart);

        return $content;
    }
    
    private function form($tableHasPart, $idHasPart, $case = "new", $value = null) 
    {
        $content[] = $tableHasPart ? [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $tableHasPart ]] : null;        
        $content[] = $idHasPart ? [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $idHasPart ]] :  null;

        $content[] = $case == "edit" ? ["tag" => "input", "attributes" => ["name" => "id", "type" => "hidden", "value" => $this->placeId]]: null;

        if ($case == "new" && $tableHasPart && $idHasPart) {
            $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => "place" ] ];
        } else {
            // name
            $content[] = self::fieldsetWithInput(_("Place"), "name", $value['name'], ["style" => "width: 320px;"]);
            // Geo
            $content[] = self::fieldsetWithInput(_("Latitude"), "latitude", $value['latitude'], ["style" => "width: 225px;"]);

            $content[] = self::fieldsetWithInput(_("Longitude"), "longitude", $value['longitude'], ["style" => "width: 225px;"]);

            // elevation
            $content[] = self::fieldsetWithInput(_("Elevation (meters)"), "elevation", $value['elevation'], ["style" => "width: 200px;"]);

            // additional type
            $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], ["style" => "width: 500px;"]);

            // description
            $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'], 100);

            // disambiguating description
            $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'], 150);

            // submit
            $content[] = self::submitButtonSend();

            if ($case == "edit") {
                $action = $tableHasPart ? "/admin/place/deleteRelationship" : "/admin/place/erase";
                $content[] = self::submitButtonDelete($action);
            }
        }
        
        return  [ "tag" => "form", "attributes" => [ "id" => "form-place-$case", "name" => "place-form-".$case, "class" => "formPadrao", "method" => "post", "action" => "/admin/place/".$case ], "content" => $content ];
    }
}
