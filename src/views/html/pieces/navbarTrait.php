<?php

namespace Plinct\Cms\View\Html\Piece;

trait navbarTrait 
{
    public $providerId;
    public $providerType;
    public $providerName;

    
    public static function navbar(string $title, array $list, $level, $appendNavbar = null)
    {
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => _($title), "append" => $appendNavbar ];
    }


    private function setProviderData($data) 
    {
        $this->providerId = filter_input(INPUT_GET, 'providerId') ?? \fwc\Thing\PropertyValueGet::getValue($data['identifier'], "fwc_id");
        $this->providerType = filter_input(INPUT_GET, 'providerType') ?? $data['@type'];
        $this->providerName = filter_input(INPUT_GET, 'providerName') ?? $data['name'];
    }
    
    public function localBusinessNavbar($id = null, $name = null, $type = null, $level = 2) {                
        if ($id && $type == "localBusiness") { 
            $list = [
                "/admin/localBusiness/edit/$id" => _("View it"),
                "/admin/service?providerId=$id&providerType=$type" => _("Services"),
                "/admin/product?providerId=$id&providerType=$type" => _("Products"),
                "/admin/offer?providerId=$id&providerType=$type" => _("Has offer catalog")
            ];
            $title = "'".$name."' "._("local business");
        } elseif ($type == "organization") { 
            $list = [
                "/admin/localBusiness?providerId=$id&providerType=$type" => _("View all"), 
                "/admin/localBusiness/new?providerId=$id&providerType=$type" => _("Add new") 
            ];
            $title = _("Locals business");
        } else { 
            $list = [
                "/admin/localBusiness" => _("View all"), 
                "/admin/localBusiness/new" => _("Add new") 
            ];
            $title = _("Locals business");
        }        
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
    
    public function navbarPlace($id = null, $name = null, $type = null, $level = 2) {
        if ($id) {
            $list = [];
            $title = "'".$name."' "._("place");
        } else {
            $list = [ 
                "/admin/place" => _("View all"), 
                "/admin/place/new" => _("Add new place")            
            ];
            $title = _("Places");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
        
    
    public function offerNavbar($id = null, $name = null, $type = null, $level = 4): array {
        if ($id ) { $list = [
                "/admin/offer?$type=$id" => _("View offers"),
                "/admin/offer/new?$type=$id" => _("Add new")
            ];
            $title = "'".$name."' "._("offers");
        } else {
            $list = [
                "/admin/offer" => _("View all"),
                "/admin/offer/new" => _("Add new")
            ];        
            $title = _("Offer");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
    
    public function organizationNavbar($id = null, $name = null, $type = null, $level = 2) {
        if ($id) { $list = [ 
                "/admin/organization/edit/$id" => _("View it"),
                "/admin/localBusiness?providerId=$id&providerType=$type" => _("Locals business"),
                "/admin/service?providerId=$id&providerType=organization" => _("Services")
            ];
            $title = "'".$name."' "._("organization");
        } else { $list = [ 
                "/admin/organization" => _("View all"), 
                "/admin/organization/new" => _("Add new organization") 
            ];
            $title = _("Organization");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
    
    public function personNavbar($id = null, $name = null, $type = null, $level = 2) {        
        if ($id) { $list = [
                "/admin/person/edit/$id" => _("View it"),
                "/admin/service?providerId=$id&providerType=person" => _("Services")
            ];
            $title = "'".$name."' "._("person");
        } else { $list = [ 
            "/admin/person" => _("View all"), "/admin/person/new" => _("Add new person") 
            ];
            $title = _("Person");
        }        
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
    

    public function serviceNavbar($id = null, $name = null, $type = null, $level = 3) {
        if ($type == "service") {
            $list = [
                "/admin/service/edit/$id?providerId=$this->providerId&providerType=$this->providerType" => _("View it"),
                "/admin/service/offer/$id?providerId=$this->providerId&providerType=$this->providerType" => _("Has offer catalog")
            ];
            $title = "'".$name."' "._("service");
        } elseif ($type == "travelAgency") {          
            $list = [
                "/admin/travelAgency/edit/$id?itemOfferedType=service" => _("View all"),
                "/admin/travelAgency/edit/$id?itemOfferedType=service&act=new" => _("Add new")
            ];
            $title = "'".$name."' "._("services");
        } elseif ($id && $name && $type) {
                $list = [
                    "/admin/services?providerId=$id&providerType=$type" => _("View all"),
                    "/admin/services/new?providerId=$id&providerType=$type" => _("Add new")
                ];
                $title = sprintf(_("Services of '%s'"), $name);
        } else {
            $list = [
                "/admin/service" => _("View all"),
                "/admin/service/new" => _("Add new")
            ];
            $title = _("Services");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
        
    public function travelActionNavbar($id = null, $name = null, $providerId = null, $level = 5) {
        if ($id && $name && $providerId) {
          $list = [ 
                "/admin/travelAction/edit/$id?providerId=$providerId&providerType=trip" => _("View it"),
                "/admin/travelAction/new?providerId=$providerId&providerType=trip" => _("Add new travelAction")
            ];
            $title = sprintf(_("'%s' travel action"),$name);   
        } else {
            $list = [ 
                "/admin/travelAction?providerId=$id&providerType=trip" => _("View all"), 
                "/admin/travelAction/new?providerId=$id&providerType=trip" => _("Add new travelAction")
            ];
            $title = sprintf(_("'%s' travel action"),$name);;
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
        
    }
    
    public static function travelAgencyNavbar($id = null, $name = null, $level = 2) 
    {
        if ($id) {
          $list = [ 
                "/admin/travelAgency/edit/".$id => _("View it"), 
                "/admin/service?providerId=$id&providerType=travelAgency" => _("Services"), 
                "/admin/product?providerId=$id&providerType=travelAgency" => _("Products"),
                "/admin/trip?providerId=$id&providerType=travelAgency" => _("Trips")
            ];
            $title = "'".$name."' "._("travel agency");   
        } else {
            $list = [ 
                "/admin/travelAgency" => _("View all"), 
                "/admin/travelAgency/new" => _("Add new travel agency"),
                "/admin/touristDestination" => _("Tourist destination"),
                "/admin/touristAttraction" => _("Tourist attraction") 
            ];
            $title = _("Travel agency");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
    
    public static function touristAttractionNavbar($id = null, $name = null, $level = 3) 
    {
        if ($id) {
          $list = [ 
                "/admin/touristAttraction/edit/".$id => _("View it"), 
            ];
            $title = "'".$name."' "._("tourist attraction");   
        } else {
            $list = [ 
                "/admin/touristAttraction" => _("View all"), 
                "/admin/touristAttraction/new" => _("Add new")
            ];
            $title = _("Tourist attraction");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }

    public static function touristDestinationNavbar($id = null, $name = null, $level = 3)
    {
        if ($id) {
            $list = [
                "/admin/touristDestination/edit/$id" => _("View it")                
            ];
            $title = "'".$name."' "._("tourist destination");
        } else {            
            $list = [ 
                "/admin/touristDestination" => _("View all"), 
                "/admin/touristDestination/new" => _("Add new")
            ];
            $title = _("Tourist destination");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }

    public function tripNavbar($id = null, $name = null, $providerId = null, $level = 4) {
        if ($id && $name && $providerId) {
          $list = [ 
                "/admin/trip/edit/$id?providerId=$providerId&providerType=$this->providerType" => _("View it"),
                "/admin/trip/itinerary/$id?providerId=$providerId&providerType=$this->providerType" => _("Itinerary"),
                //"/admin/trip/service/$id?providerId=$this->providerId&providerType=$this->providerType" => _("Services"),
                "/admin/trip/offer/$id?providerId=$this->providerId&providerType=$this->providerType" => _("Offers")
            ];
            $title = sprintf(_("'%s' trips"),$name);   
        } else {
            $list = [ 
                "/admin/trip?providerId=$id&providerType=travelAgency" => _("View all"), 
                "/admin/trip/new?providerId=$id&providerType=travelAgency" => _("Add new trip")
            ];
            $title = _("Trips");
        }
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => $title ];
    }
}
