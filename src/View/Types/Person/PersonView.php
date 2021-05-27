<?php
namespace Plinct\Cms\View\Types\Person;

use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\Intangible\PostalAddressView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class PersonView {
    protected $content;
        
    use navbarTrait;
    use FormElementsTrait;

    public function navbarPerson($id = null, $name = null) {
        $title = _("Person");
        $list = [ "/admin/person" => _("View all"), "/admin/person/new" => _("Add new person") ];
        $search = self::searchPopupList("person");
        $this->content['navbar'][] = self::navbar($title, $list, 2, $search);
        if($id) {
            $this->content['navbar'][] = self::navbar($name, [], 3);
        }
    }
    
    public function index(array $data): array {
        $this->navbarPerson();
        $this->content['main'][] = self::listAll($data, "person", _("List of persons"), [ "dateModified" => "Date modified" ]);
        return $this->content;
    }
    
    public function new(): array {
        $this->navbarPerson();
        $this->content['main'][] = [ "tag" => "h3", "content" => _("Add new person") ];
        $this->content['main'][] = self::formPerson();
        return $this->content;
    }
    
    public function edit(array $data): array {
        $value = $data[0];
        $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $this->navbarPerson($id, $value['name'] ?? "ND");
        // FORM
        $this->content['main'][] = self::formPerson('edit', $value);
        // CONTACT POINT
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new ContactPointView())->getForm('person', $id, $value['contactPoint']) ]);
        // ADDRESS
        $this->content['main'][] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("person", $id, $value['address']) ]);
        // IMAGE
        $this->content['main'][] = self::divBoxExpanding(_("Image"), "ImageObject", [ (new ImageObjectView())->getForm("Person",$id,$value['image']) ]);
        return $this->content;
    }
    
    private static function formPerson($case = 'new', $value = null, $tableHasPart = null, $idHasPart = null): array {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], 'id')['value'] : null;
        $content[] = [ "tag" => "h6", "content" => ucfirst(_($case)) ];
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name"=>"id", "type" => "hidden", "value" => $id ] ] : null ;
        if ($tableHasPart) {
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableHasPart", "type" => "hidden", "value"=>$tableHasPart ] ] ;
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idHasPart", "type" => "hidden", "value"=>$idHasPart ] ] ;
        }
        // GIVEN NAME
        $attributes = [ "name"=>"givenName", "type" => "text", "value" => $value['givenName'] ?? "" ];
        $attr = $case !== "edit" ? array_merge($attributes, [ "data-idselect" => "gv".($id ?? $case), "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off" ]) : $attributes;
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "min-width: 380px;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Given name") ],
            [ "tag" => "input", "attributes" => $attr ] 
        ]];
        // family name
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Family name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"familyName", "type" => "text", "value"=>$value['familyName'] ?? null ] ]
        ]];
        // additional name
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Additional name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"additionalName", "type" => "text", "value"=>$value['additionalName'] ?? null ] ]
        ]];
        // Tax ID
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Tax ID") ],
            [ "tag" => "input", "attributes" => [ "name"=>"taxId", "type" => "text", "value"=>$value['taxId'] ?? null ] ]
        ]];
        // birth date
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Birth date") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthDate", "type" => "date", "value"=>$value['birthDate'] ?? null ] ]
        ]];
        // birth place
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Birth place") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthPlace", "type" => "text", "value"=>$value['birthPlace'] ?? null ] ]
        ]];
        // gender
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" =>"legend", "content" => _("Gender") ],
            [ "tag" => "input", "attributes" => [ "name"=>"gender", "type" => "text", "value"=>$value['gender'] ?? null ] ]
        ]];
        // has occupation
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 400px;" ], "content" => [ 
            [ "tag" =>"legend", "content" => _("Has occupation") ],
            [ "tag" => "input", "attributes" => [ "name"=>"hasOccupation", "type" => "text", "value" => $value['hasOccupation'] ?? null ] ]
        ]];
        
        // submit
        $content[] = self::submitButtonSend();
        if ($case !== "add") {
            $content[] = self::submitButtonDelete("/admin/person/erase");
        }
        return [ "tag" => "form", "attributes" => [ "class" => "form-inline box", "action" => "/admin/person/$case", "method" => "post"], "content" => $content ];
    }
}
