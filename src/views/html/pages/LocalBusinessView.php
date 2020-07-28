<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class LocalBusinessView
{
    protected $content;
    public $localBusinessId;
    public $localBusinessName;

    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;
    
    public function navbar() 
    {
        if ($this->providerType ==  "organization") {
            $this->content['navbar'][] = $this->organizationNavbar($this->providerId, $this->providerName, $this->providerType, 2);
            $this->content['navbar'][] = $this->localBusinessNavbar($this->providerId, $this->providerName, $this->providerType, 3);
        } else {
            $this->content['navbar'][] = $this->localBusinessNavbar();
        }
        if ($this->localBusinessId) {
            $this->content['navbar'][] = $this->localBusinessNavbar($this->localBusinessId, $this->localBusinessName, "localBusiness", 3);
        } 
    }

    public function index($data): array
    {        
        $this->navbar();
        
        $this->content['main'][] = self::listAll($data, "localBusiness", "LocalBusiness list", [ "dateModified" => "Date modified" ]);
                
        return $this->content;
    }
    
    public function new($data = null): array
    {
        $this->navbar();        
        
        $this->content['main'][] = self::divBox(_("Localbusiness"), "LocalBusiness", [ self::form() ]);        
        
        return $this->content;
    }
    
    public function edit($data): array
    {        
        $value = $data[0];
        
        $id = PropertyValue::extractValue($value['identifier'], "id");
        
        $this->navbar();
        
        $this->content['main'][] = self::divBox(_("LocalBusiness"), "LocalBusiness", [ self::form("edit", $value) ]);
        
        // place
        $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [ (new PlaceView())->getForm("localBusiness", $id, $value['location'])]);
        
        // Contact Point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new contactPointView())->getForm("localBusiness", $id, $value['contactPoint']) ]); 
        
        // organization
        $this->content['main'][] = self::divBoxExpanding(_("Organization"), "Organization", [ (new OrganizationView())->getForm('localBusiness', $id, $value['organization']) ]);
        
        // person
        $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [ (new PersonView())->getForm("localBusiness", $id, $value['member']) ]);
        
        // images
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "imageObject", [ (new ImageObjectView())->getForm("localBusiness", $id, $value['image']) ]);
        
        return $this->content;
    }
    
    /*public function getForm($value)
    {
        $this->localBusinessId = $this->localBusinessId ?? PropertyValue::extractValue($value['identifier'], "id");
        $this->localBusinessName = $this->localBusinessName ?? $value['name'];
        
        // local business
        $content[] = self::divBox("'".$this->localBusinessName."' "._("local business"), "LocalBusiness", [self::form('edit', $value, $this->localBusinessId)]);
        
        // place
        $content[] = self::divBox("'".$this->localBusinessName."' "._("local business"), "Place", [ (new PlaceView())->getForm("localBusiness", $this->localBusinessId, $value['location']) ]);
        
        // Contact Point
        $content[] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new contactPointView())->getForm("localBusiness", $this->localBusinessId, $value['contactPoint']) ]); 
        
        // organization
        $content[] = self::divBoxExpanding(_("Organization"), "Organization", [ (new OrganizationView())->getForm('localBusiness', $this->localBusinessId, $value['organization']) ]);
        
        // person
        $content[] = self::divBoxExpanding(_("Persons"), "Person", [ (new PersonView())->getForm("localBusiness", $this->localBusinessId, $value['member']) ]);
        
        // images
        $content[] = self::divBoxExpanding(_("Images"), "imageObject", [ (new ImageObjectView())->getForm("localBusiness", $this->localBusinessId, $value['image']) ]);

        return $content;
    }*/


    private static function form($case = "new", $value = null, $ID = null) 
    { 
        $id = PropertyValue::extractValue($value['identifier'], "id");
                
        if ($case == "edit") {
            $content[] = self::input("id", "hidden", $id);
            
        }
        
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'], [ "style" => "width: 50%" ]);
        
        // additionalType
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "width: 50%" ]);
        
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description']);
        
        // disambiguatingDescription
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription']);
        
        // hasOfferCatalog
        $content[] = self::fieldsetWithInput( _("Offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'], [ "style" => "width: calc(100% - 400px);" ]);
        
        // dateCreated
        $content[] = self::fieldsetWithInput( _("Date created"), "dateCreated", $value['dateCreated'], [ "style" => "width: 200px" ], "datetime", [ "readonly" ]);
        
        // dateModified
        $content[] = self::fieldsetWithInput( _("Date modified"), "dateModified", $value['dateModified'], [ "style" => "width: 200px" ], "datetime", [ "readonly" ]);
        
        // url
        $content[] = self::fieldsetWithInput( "url", "url", $value['url'], [ "style" => "width: 50%" ]);
        
        $content[] = self::submitButtonSend();
        
        if ($case == "edit") {
            $content[] = self::submitButtonDelete("/admin/localBusiness/erase");
        }
        
        return [ "tag" => "form", "attributes" => [ "id" => "localBusiness-form", "class" => "formPadrao", "action" => "/admin/localBusiness/$case", "method" => "post" ], "content" => $content ];
    }
}
