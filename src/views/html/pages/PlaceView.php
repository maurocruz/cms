<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class PlaceView
{
    protected $placeId;
    
    protected $placeName;

    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;
    
    public function navbar() 
    {
        $this->content['navbar'][] = $this->navbarPlace();
        
        if ($this->placeId) {
            $this->content['navbar'][] = $this->navbarPlace($this->placeId, $this->placeName, null, 3);
        }
    }

    public function index(array $data): array
    {   
        $this->navbar();
        
        $this->content['main'][] = self::listAll($data, "place", _("Places"), [ "additionalType" => _("Additional type") ]);
        
        return $this->content;
    }
    
    public function add($data = null): array
    {
        $this->navbar();
        
        $this->content['main'][] = self::divBox(_("Add new"), "Place", [ self::form(null, null) ]);
        
        return $this->content;
    }

    public function edit(array $data): array
    {
        if ($data['@context'] !== null) {
            $this->placeId = PropertyValue::extractValue($data['identifier'], "id");
            
            $this->placeName = $data['name'];

            //place
            $place[] = self::form(null, null, 'edit', $data);

            // address
            $place[] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("Place", $this->placeId, $data['address']) ]);
            
            // images
            $place[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("place", $this->placeId, $data['image']) ]);

            // append
            $this->content['main'][] = self::divBox($data['name'], 'place', $place);
            
        } else {
             $this->content['main'][] = self::noContent();
        }
        
        $this->navbar();
        
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
        
        if ($case == "edit" ) {
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $this->placeId ] ];
        }   
        
        // name        
        $content[] = $case == "addWithPart" ? self::searchAndSubmit("name", "place", "name", $value['location'] ?? $value) : self::fieldsetWithInput(_("Place"), "name", $value['name'], [ "style" => "width: 320px;"]);        
        // Geo
        $content[] = self::fieldsetWithInput(_("Latitude"), "latitude", $value['latitude'], [ "style" => "width: 225px;"]);
        
        $content[] = self::fieldsetWithInput(_("Longitude"), "longitude", $value['longitude'], [ "style" => "width: 225px;"]);
        
        // elevation
        $content[] = self::fieldsetWithInput(_("Elevation (meters)"), "elevation", $value['elevation'], [ "style" => "width: 200px;"]);
        
        // additional type
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "width: 500px;"]);
        
        // description
        //$content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'], 100);
        
        // disambiguating description
        //$content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'], 150);
        
        // submit
        $content[] = self::submitButtonSend();
        
        if ($case == "edit") {
            $action = $tableHasPart ? "/admin/place/deleteRelationship" : "/admin/place/erase";
            $content[] = self::submitButtonDelete($action);
        }
        
        return  [ "tag" => "form", "attributes" => [ "id" => "form-place-$case", "name" => "place-form-".$case, "class" => "formPadrao", "method" => "post", "action" => "/admin/place/".$case ], "content" => $content ];
    }
}
