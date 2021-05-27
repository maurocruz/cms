<?php
namespace Plinct\Cms\View\Types\Intangible;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class ContactPointView {
    use FormElementsTrait;
        
    public function getForm($tableHasPart, $idHasPart, $data): array {
        if ($data) {
            foreach ($data as $key => $value) {
                $content[] = self::formContactPoint($tableHasPart, $idHasPart, "edit", $value);
                $content[] = [ "tag" => "hr" ]; 
            }
        }        
        $pos = isset($key) ? ($key+2) : 1;
        $content[] = self::formContactPoint($tableHasPart, $idHasPart, "new", null, $pos );
        return $content;
    }
    
    static private function formContactPoint($tableHasPart, $idHasPart, $case = 'new', $value = null, $key = null): array {
        $content[] = self::input('tableHasPart', "hidden", $tableHasPart);
        if ($case === "new") {
            $content[] = _("new").": ";
            $content[] = self::input('idHasPart', "hidden", $idHasPart);
        } else {     
            $id = ArrayTool::searchByValue($value['identifier'],"id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // POSITION
        $content[] = self::fieldsetWithInput("#", "position", $value['position'] ?? $key, [ "style" => "width: 50px;" ], "number", [ "min" => "1"]);
        // NAME
        $content[] = self::fieldsetWithInput(_("Contact name"), "name", $value['name'] ?? null,[ "style" => "width: 120px;" ]);
        // CONTACT TYPE
        $content[] = self::fieldsetWithInput(_("Contact type"), "contactType", $value['contactType'] ?? null, [ "style" => "width: 120px;" ]);
        // TELEPHONE
        $content[] = self::fieldsetWithInput(_("Telephone"), "telephone", $value['telephone'] ?? null, [ "style" => "width: 140px;" ]);
        // WHATSAPP
        $whatsapp = isset($value['whatsapp']) && $value['whatsapp'] === '1' ? "checked" : null;
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Whatsapp" ],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '1', $whatsapp ] ], " Sim"
            ]],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '0', $whatsapp ] ], " Não"
            ]]
        ]];
        // EMAIL
        $content[] = self::fieldsetWithInput(_("Email"), "email", $value['email'] ?? null, [ "style" => "width: 240px;" ]);
        // OBS
        $content[] = self::fieldsetWithInput(_("Obs"), "obs", $value['obs'] ?? null, [ "style" => "width: calc(95% - 810px);" ]);
        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/contactPoint/erase") : null;
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/contactPoint/$case", "method" => "post" ], "content" => $content ];
    }
}
