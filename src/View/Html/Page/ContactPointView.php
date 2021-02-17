<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;

class ContactPointView
{
    use FormElementsTrait;
        
    public function getForm($tableHasPart, $idHasPart, $data): array
    {        
        if ($data) {
            foreach ($data as $key => $value) {
                $content[] = self::formContacPoint($tableHasPart, $idHasPart, "edit", $value);
                $content[] = [ "tag" => "hr" ]; 
            }
        }        
        $pos = isset($key) ? ($key+2) : 1;
        $content[] = self::formContacPoint($tableHasPart, $idHasPart, "new", null, $pos );
        return $content;
    }
    
    static private function formContacPoint($tableHasPart, $idHasPart, $case = 'new', $value = null, $key = null): array
    {
        $content[] = self::input('tableHasPart', "hidden", $tableHasPart);
        if ($case === "new") {
            $content[] = _("new").": ";
            $content[] = self::input('idHasPart', "hidden", $idHasPart);
        } else {     
            $id = PropertyValue::extractValue($value['identifier'],"id");
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // POSITION
        $content[] = self::fieldsetWithInput("#", "position", $value['position'], [ "style" => "width: 50px;" ], "number", [ "min" => "1"]);
        // NAME
        $content[] = self::fieldsetWithInput(_("Contact name"), "name", $value['name'],[ "style" => "width: 120px;" ]);
        // CONTACT TYPE
        $content[] = self::fieldsetWithInput(_("Contact type"), "contactType", $value['contactType'], [ "style" => "width: 120px;" ]);
        // TELEPHONE
        $content[] = self::fieldsetWithInput(_("Telephone"), "telephone", $value['telephone'], [ "style" => "width: 140px;" ]);
        // WHATSAPP
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Whatsapp" ],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '1', $value['whatsapp'] === '1' ? "checked" : null ] ], " Sim"                
            ]],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '0', $value['whatsapp'] !== '1' ? "checked" : null ] ], " Não"                
            ]]
        ]];
        // EMAIL
        $content[] = self::fieldsetWithInput(_("Email"), "email", $value['email'], [ "style" => "width: 240px;" ]);
        // OBS
        $content[] = self::fieldsetWithInput(_("Obs"), "obs", $value['obs'], [ "style" => "width: calc(95% - 810px);" ]);
        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/contactPoint/erase") : null;
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/contactPoint/$case", "method" => "post" ], "content" => $content ];
    }
}
