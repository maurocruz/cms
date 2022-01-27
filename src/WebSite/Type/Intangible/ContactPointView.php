<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Tool\ArrayTool;

class ContactPointView
{
    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $data
     * @return array
     */
    public function getForm($tableHasPart, $idHasPart, $data): array
    {
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

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param string $case
     * @param $value
     * @param $key
     * @return array
     */
    static private function formContactPoint($tableHasPart, $idHasPart, string $case = 'new', $value = null, $key = null): array
    {
        $form = Fragment::form(["class" => "formPadrao form-contactPoint"]);
        $form->action("/admin/contactPoint/$case")->method("post");
        // hiddens
        $form->input('tableHasPart', $tableHasPart, "hidden");
        if ($case === "new") {
            $form->input('idHasPart', (string) $idHasPart, "hidden");
            $form->content("<h4>"._("new").": </h4>");
        } else {     
            $id = ArrayTool::searchByValue($value['identifier'],"id")['value'];
            $form->input('id', $id, 'hidden');
        }
        // POSITION
        $form->fieldsetWithInput("position", (isset($value['position']) ? (string) $value['position'] : (string) $key), "#", "number", null, [ "min" => "1"]);
        // NAME
        $form->fieldsetWithInput("name", $value['name'] ?? null, _("Contact name"));
        // CONTACT TYPE
        $form->fieldsetWithInput("contactType", $value['contactType'] ?? null, _("Contact type"));
        // TELEPHONE
        $form->fieldsetWithInput("telephone", $value['telephone'] ?? null, _("Telephone"));
        // WHATSAPP
        $whatsapp = $value['whatsapp'] ?? null;
        $form->content([ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Whatsapp" ],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '1', $whatsapp == '1' ? "checked" : null ] ], " Sim "
            ]],
            [ "tag" => "label", "content" => [                
                [ "tag" => "input", "attributes" => [ "name" => "whatsapp", "type" => "radio", "value" => '0', $whatsapp != '1' ? "checked" : null ] ], " Não "
            ]]
        ]]);
        // EMAIL
        $form->fieldsetWithInput("email", $value['email'] ?? null, _("Email"));
        // OBS
        $form->fieldsetWithInput("obs", $value['obs'] ?? null, _("Obs"));
        // SUBMIT
        $form->submitButtonDelete();
        if ($case == "edit") $form->submitButtonDelete("/admin/contactPoint/erase");
        // READY
        return $form->ready();
    }
}
