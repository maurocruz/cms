<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Web\Widget\FormTrait;

class contactPointView
{
    use FormTrait;
        
    public function getForm($tableHasPart, $idHasPart, $data)
    {        
        if ($data) {
            foreach ($data as $key => $value) {
                $content[] = self::form($tableHasPart, $idHasPart, "edit", $value);
                $content[] = [ "tag" => "hr" ]; 
            }
        }        
        $pos = isset($key) ? ($key+2) : 1;
        $content[] = self::form($tableHasPart, $idHasPart, "new", null, $pos );
        
        return $content;
    }
    
    static private function form($tableHasPart, $idHasPart, $case = 'new', $value = null, $key = null)
    {
        $obs = isset($value['contactOption']) ? PropertyValue::extractValue($value['contactOption'],"obs") : null;

        $content[] = self::input('tableHasPart', "hidden", $tableHasPart);

        if ($case === "new") {
            $content[] = _("new").": ";
            $content[] = self::input('idHasPart', "hidden", $idHasPart);
            
        } else {     
            $id = PropertyValue::extractValue($value['identifier'],"id");
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        
        // position
        $content[] = [ "tag" => "fieldset","attributes" => [ "style" => "width: 20px;" ], "content" => [
            [ "tag" => "legend", "content" => "Pos." ],            
            [ "tag" => "input", "attributes" => [ "name" => "position", "type" => "number", "min" => "1", "value" => $value['position'] ?? $key ] ]
        ]];
        
        // name
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 120px;" ], "content" => [
            [ "tag" => "legend", "content" => "Contact name" ],
            [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ?? null ] ]
        ]];
        
        // contact type
        $content[] = [ "tag" => "fieldset","attributes" => [ "style" => "width: 120px;" ], "content" => [
            [ "tag" => "legend", "content" => "Contact type" ],
            [ "tag" => "input", "attributes" => [ "name" => "contactType", "type" => "text", "value" => $value['contactType'] ?? null ] ]
        ]]; 
        
        // telephone
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 140px;" ],"content" => [
            [ "tag" => "legend", "content" => "Telephone" ],
            [ "tag" => "input", "attributes" => [ "name" => "telephone", "type" => "text", "value" => $value['telephone'] ?? null ] ]
        ]]; 
        
        // whatsapp
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Whatsapp" ],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '1', $value['whatsapp'] === '1' ? "checked" : null ] ], " Sim"                
            ]],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '0', $value['whatsapp'] !== '1' ? "checked" : null ] ], " Não"                
            ]]
        ]];  
        
        // email
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 240px;" ], "content" => [
            [ "tag" => "legend", "content" => "Email" ],
            [ "tag" => "input", "attributes" => [ "name" => "email", "type" => "text", "value" => $value['email'] ?? null ] ]
        ]]; 
        
        // obs
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(95% - 850px);" ],"content" => [
            [ "tag" => "legend", "content" => "Obs." ],
            [ "tag" => "input", "attributes" => [ "name" => "obs", "type" => "text", "value" => $obs ] ]
        ]]; 
        
        $content[] = self::submitButtonSend();
        
        if ($case == "edit") {
            $content[] = self::submitButtonDelete("/admin/contactPoint/erase");
        }    
        
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/contactPoint/$case", "method" => "post" ], "content" => $content ];
    }
}
