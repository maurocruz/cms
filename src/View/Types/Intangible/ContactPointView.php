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
            $content[] = "<h4>"._("new").": </h4>";
            $content[] = self::input('idHasPart', "hidden", $idHasPart);
        } else {     
            $id = ArrayTool::searchByValue($value['identifier'],"id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // POSITION
        $content[] = self::fieldsetWithInput("#", "position", $value['position'] ?? $key, null, "number", [ "min" => "1"]);
        // NAME
        $content[] = self::fieldsetWithInput(_("Contact name"), "name", $value['name'] ?? null);
        // CONTACT TYPE
        $content[] = self::fieldsetWithInput(_("Contact type"), "contactType", $value['contactType'] ?? null);
        // TELEPHONE
        $content[] = self::fieldsetWithInput(_("Telephone"), "telephone", $value['telephone'] ?? null);
        // WHATSAPP
        $whatsapp = $value['whatsapp'] ?? null;
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Whatsapp" ],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '1', $whatsapp == '1' ? "checked" : null ] ], " Sim "
            ]],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '0', $whatsapp != '1' ? "checked" : null ] ], " Não "
            ]]
        ]];
        // EMAIL
        $content[] = self::fieldsetWithInput(_("Email"), "email", $value['email'] ?? null);
        // OBS
        $content[] = self::fieldsetWithInput(_("Obs"), "obs", $value['obs'] ?? null);
        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/contactPoint/erase") : null;
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-contactPoint", "action" => "/admin/contactPoint/$case", "method" => "post" ], "content" => $content ];
    }
}
