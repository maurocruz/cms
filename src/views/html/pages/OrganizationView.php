<?php

namespace Plinct\Cms\View\Html\Page;

class OrganizationView
{
    protected $content;
    protected $organizationId;
    protected $organizationName;

    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;
    
    public function navbar() {
        $this->content['navbar'][] = $this->organizationNavbar();
        if ($this->organizationId) {
            $this->content['navbar'][] = $this->organizationNavbar($this->organizationId, $this->organizationName, "organization", 3);
        }
    }
    
    public function getForm($tableOwner, $idOwner, $value) 
    {
        $content[] = self::input('tableOwner', "hidden", $tableOwner);
        $content[] = self::input('idOwner', "hidden", $idOwner);
        $content[] = self::fieldsetWithInput(_("Organization"), "organization", $value['name'], [ "style" => "min-width: 320px;" ], "text", [ "data-type" => "organization", "data-property" => "name", "onkeyup" => "searchNameAndInputId(event);", "autocomplete" => "off" ]);
        if ($value) {
            $content[] = self::input("organization", "hidden", \fwc\Thing\PropertyValueGet::getValue($value['identifier'], "id"));
            $content[] = self::submitButtonDelete("/admin/organization/eraseWithPartOf");
        }
        return self::form('/admin/organization/addWithPartOf', $content);
    }
    
    public function index(array $data): array
    {        
        $this->navbar();
        
        if (isset($data['errorInfo'])) {
            $this->content['main'][] = self::errorInfo($data['errorInfo'], "organization");
            
        } else {
            $this->content['main'][] = self::listAll($data, "organization", _("List of organizations"), [ "update_time" => "Update date" ]);
        }
        
        return $this->content;
    }
    
    public function new($data = null): array
    {
        $this->navbar();
        
        $this->content['main'][] = self::divBox(_("Add organization"), "Organization", [ self::formOrganization() ]);
        
        return $this->content;
    }

    public function edit(array $data): array
    {
        $value = $data[0];
        
        $this->organizationId = \Plinct\Api\Type\PropertyValue::extractValue($value['identifier'], "id");
        $this->organizationName = $value['name'];
        
        $this->navbar();
        
        // organization
        $this->content['main'][] = self::formOrganization('edit', $value);
        // address
        $this->content['main'][] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("organization", $this->organizationId, $value['address']) ]);
        // contact point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new contactPointView())->getForm('organization', $this->organizationId, $value['contactPoint']) ]);
        // member
        $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [ (new PersonView())->getForm("organization", $this->organizationId, $value['member']) ]);
        // location
        $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [ (new PlaceView())->getForm("organization", $this->organizationId, $value['location']) ]);
        // areaServed
        //$this->content['main'][] = self::divBoxExpanding(_("Area served"), "Place", [ (new PlaceView())->getForm("organization", $this->organizationId, $value['location']) ]);
        // image
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("organization", $this->organizationId. $value['image']) ]);
        
        return $this->content;
    }
    
    static private function formOrganization($case = 'new', $value = null) 
    {
        $content[] = [ "tag" => "h3", "content" => $value['name'] ];
        
        if ($case == "edit") {
            $ID = \Plinct\Api\Type\PropertyValue::extractValue($value['identifier'], 'id');
            
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOrganization", "type" => "hidden", "value" => $ID ] ];            
        }
        
        // legal name
        $content[] = self::fieldsetWithInput(_("Legal Name"), "legalName", $value['legalName'], [ "style" => "width: 32%;" ]);
        
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'], [ "style" => "width: 32%;" ]);
        
        // tax id
        $content[] = self::fieldsetWithInput(_("Tax Id"), "taxId", $value['taxId'], [ "style" => "width: 32%;" ]);
        
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'], 100 );
        
        // additional type
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "min-width: 320px; width: 30%;" ]);
        
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'], [ "style" => "width: 30%;" ]);
        
        //submit
        $content[] = self::submitButtonSend();
        
        if ($case == "edit") {
            $content[] = self::submitButtonDelete('/admin/organization/delete');
        }
        
        return [ "tag" => "form", "attributes" => [ "name" => "form-organization", "id" => "form-organization", "class" => "formPadrao box", "action" => "/admin/organization/$case", "method" => "post" ], "content" => $content ];
    }
}
