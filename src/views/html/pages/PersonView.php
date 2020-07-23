<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class PersonView
{
    protected $content;
    
    protected $personId;
    
    protected $personName;
    
    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;

    public function navbar() 
    {
        $this->content['navbar'][] = $this->personNavbar();
        
        if($this->personId) {
            $this->content['navbar'][] = $this->personNavbar($this->personId, $this->personName, null, 3);
        }
    }
    
    public function index(array $data): array
    {
        $this->navbar();
        
        $this->content['main'][] = self::listAll($data, "person", _("List of persons"));
        
        return $this->content;
    }
    
    public function add($data = null): array
    {
       $this->content['main'][] = [ "tag" => "h3", "content" => _("Add new person") ];
       $this->content['main'][] = self::form();
       
       return $this->content;
    }
    
    public function edit(array $data): array 
    {       
        $this->personId = PropertyValue::extractValue($data['identifier'], "fwc_id");
        $this->personName = $data['name'];
        
        $this->navbar();
        
        // form
        $this->content['main'][] = self::form('edit', $data);
        
        // contact point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new contactPointView())->getForm('person', $this->personId, $data['contactPoint']) ]);
        
        // address
        $this->content['main'][] = self::divBoxExpanding(_("Postal address"), "PostalAddres", [ (new PostalAddressView())->getForm("person", $this->personId, $data['address']) ]);
        
        return $this->content;
    }
    
    public function getForm($tableOwner, $idOwner, $data) 
    {        
        if ($data) {
            // show persons
            foreach ($data as $value) {
                $content[] = self::formWithPartOf($value, $tableOwner, $idOwner);
            }
        }        
        // insert new person
        $content[] = self::form("addWithPartOf", null, $tableOwner, $idOwner);        
        return $content;
    }
    
    private function form($case = 'new', $value = null, $tableOwner = null, $idOwner = null)
    {
        $content[] = [ "tag" => "h6", "content" => ucfirst(_($case)) ]; 
        
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name"=>"idperson", "type" => "hidden", "value" => $this->personId ] ] : null ;
        
        if ($tableOwner) {
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableOwner", "type" => "hidden", "value"=>$tableOwner ] ] ;
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idOwner", "type" => "hidden", "value"=>$idOwner ] ] ;
        }
        
        // given name
        $attributes = [ "name"=>"givenName", "type" => "text", "value" => $value['givenName'] ?? "" ];
        
        $attr = $case !== "edit" ? array_merge($attributes, [ "data-idselect" => "gv".($ID ?? $case), "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off" ]) : $attributes;
        
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "min-width: 380px;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Given name") ],
            [ "tag" => "input", "attributes" => $attr ] 
        ]];
        
        // family name
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Family name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"familyName", "type" => "text", "value"=>$value['familyName'] ] ] 
        ]];
        
        // additional name
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Additional name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"additionalName", "type" => "text", "value"=>$value['additionalName'] ] ] 
        ]];
        
        // Tax ID
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Tax ID") ],
            [ "tag" => "input", "attributes" => [ "name"=>"taxId", "type" => "text", "value"=>$value['taxId'] ] ] 
        ]];
        
        // birth date
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Birth date") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthDate", "type" => "date", "value"=>$value['birthDate'] ] ] 
        ]];
        
        // birth place
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Birth place") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthPlace", "type" => "text", "value"=>$value['birthPlace'] ] ] 
        ]];
        
        // gender
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Gender") ],
            [ "tag" => "input", "attributes" => [ "name"=>"gender", "type" => "text", "value"=>$value['gender'] ] ] 
        ]];
        
        // has occupation
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 400px;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Has occupation") ],
            [ "tag" => "input", "attributes" => [ "name"=>"hasOccupation", "type" => "text", "value" => $value['hasOccupation'] ] ] 
        ]];
        
        // submit
        $content[] = self::submitButtonSend();
        
        if ($case !== "add") {
            $content[] = self::submitButtonDelete("/admin/person/erase");
        }
        
        return [ "tag" => "form", "attributes" => [ "class" => "form-inline box", "action" => "/admin/person/$case", "method" => "post"], "content" => $content ];
    }
    
    static private function formWithPartOf($value, $tableOwner, $idOwner) 
    {
        $ID = PropertyValue::extractValue($value['identifier'], "fwc_id");
        
        $content[] = [ "tag" => "h3", "content" => ucfirst(_("Person")) ]; 
        
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idperson", "type" => "hidden", "value" => $ID ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableOwner", "type" => "hidden", "value" => $tableOwner ] ] ;
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idOwner", "type" => "hidden", "value" => $idOwner ] ];
        
        // given name
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 64%;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Name")." - "._("taxId")." [<a href=\"/admin/modules/person/edit/".$ID."\" class=\"white\">"._("Edit person")."</a>]" ],
            [ "tag" => "input", "attributes" => [ "name"=>"listPersonNames", "type" => "text", "value" => $value['name'], "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off", "readonly" ] ]
        ]];
        
        // jobTitle
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 23%;" ], "content" => [ 
                [ "tag" =>"legend", "content" => _("Job title") ],
                [ "tag" => "input", "attributes" => [ "name"=>"jobTitle", "type" => "text", "value" => $value['jobTitle'] ?? null ] ]
            ]]; 
        
        $content[] = self::submitButtonSend();
        
        $content[] = self::submitButtonDelete("/admin/person/deleteWithPartOf");
        
        return [ "tag" => "form", "attributes" => [ "class" => "form-inline box", "action" => "/admin/person/putRelationship", "method" => "post" ], "content" => $content ];
    }
}
