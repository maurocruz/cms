<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class PersonView
{
    protected $content;
        
    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;

    public function navbar($id = null, $name = null) 
    {
        $this->content['navbar'][] = $this->personNavbar();
        
        if($id) {
            $this->content['navbar'][] = $this->personNavbar($id, $name, null, 3);
        }
    }
    
    public function index(array $data): array
    {
        $this->navbar();
        
        $this->content['main'][] = self::listAll($data, "person", _("List of persons"), [ "dateModified" => "Date modified" ]);
        
        return $this->content;
    }
    
    public function new($data = null): array
    {
        $this->navbar();
        
        $this->content['main'][] = [ "tag" => "h3", "content" => _("Add new person") ];
        $this->content['main'][] = self::form();
       
        return $this->content;
    }
    
    public function edit(array $data): array 
    {
        $value = $data[0];
        
        $id = PropertyValue::extractValue($value['identifier'], "id");
        
        $this->navbar($id, $value['name']);
        
        // form
        $this->content['main'][] = self::form('edit', $value);
        
        // contact point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new contactPointView())->getForm('person', $id, $value['contactPoint']) ]);
        
        // address
        $this->content['main'][] = self::divBoxExpanding(_("Postal address"), "PostalAddres", [ (new PostalAddressView())->getForm("person", $id, $value['address']) ]);
        
        return $this->content;
    }
    
    public function getForm($tableHasPart, $idHasPart, $data) 
    {        
        if ($data) {
            // show persons
            foreach ($data as $value) {
                $content[] = self::form('edit', $value, $tableHasPart, $idHasPart);
            }
        }        
        // insert new person
        $content[] = self::form("new", null, $tableHasPart, $idHasPart);        
        return $content;
    }
    
    private static function form($case = 'new', $value = null, $tableHasPart = null, $idHasPart = null)
    {
        $id = PropertyValue::extractValue($value['identifier'], 'id');
        
        $content[] = [ "tag" => "h6", "content" => ucfirst(_($case)) ]; 
        
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name"=>"id", "type" => "hidden", "value" => $id ] ] : null ;
        
        if ($tableHasPart) {
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableHasPart", "type" => "hidden", "value"=>$tableHasPart ] ] ;
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idHasPart", "type" => "hidden", "value"=>$idHasPart ] ] ;
        }
        
        // given name
        $attributes = [ "name"=>"givenName", "type" => "text", "value" => $value['givenName'] ?? "" ];
        
        $attr = $case !== "edit" ? array_merge($attributes, [ "data-idselect" => "gv".($id ?? $case), "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off" ]) : $attributes;
        
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
    
    private static function formWithPartOf($value, $tableHasPart, $idHasPart) 
    {
        $id = PropertyValue::extractValue($value['identifier'], "id");
        
        $content[] = [ "tag" => "h3", "content" => ucfirst(_("Person")) ]; 
        
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"id", "type" => "hidden", "value" => $id ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableHasPart", "type" => "hidden", "value" => $tableHasPart ] ] ;
        $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idHasPart", "type" => "hidden", "value" => $idHasPart ] ];
        
        // given name
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 64%;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Name")." - "._("taxId")." [<a href=\"/admin/modules/person/edit/".$id."\" class=\"white\">"._("Edit person")."</a>]" ],
            [ "tag" => "input", "attributes" => [ "name"=>"listPersonNames", "type" => "text", "value" => $value['name'], "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off", "readonly" ] ]
        ]];
        
        // jobTitle
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 23%;" ], "content" => [ 
                [ "tag" =>"legend", "content" => _("Job title") ],
                [ "tag" => "input", "attributes" => [ "name"=>"jobTitle", "type" => "text", "value" => $value['jobTitle'] ?? null ] ]
            ]]; 
        
        $content[] = self::submitButtonSend();
        
        $content[] = self::submitButtonDelete("/admin/person/erase");
        
        return [ "tag" => "form", "attributes" => [ "class" => "form-inline box", "action" => "/admin/person/edit", "method" => "post" ], "content" => $content ];
    }
}
