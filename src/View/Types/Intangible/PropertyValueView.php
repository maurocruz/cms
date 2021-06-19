<?php
namespace Plinct\Cms\View\Types\Intangible;

use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\FormTrait;

class PropertyValueView {
    use FormTrait;
    
    public function getForm($tableHasPart, $idHasPart, $data): array {
        foreach ($data as $value) {
            if (isset($value['identifier'])) {
                    $content[] = self::formPropertyValue($tableHasPart, $idHasPart, 'edit', $value);
            }
        }
        // new
        $content[] = self::formPropertyValue($tableHasPart, $idHasPart);
        return $content;
    }
    
    protected function formPropertyValue($tableHasPart, $idHasPart, $case = "new", $value = null): array {
        $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $tableHasPart ] ];
        if ($case == "new") {
            $contentForm[] = "Novo: ";
            $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $idHasPart ] ];
            
        } else {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // NAME
        $contentForm[] = [ "tag" => "fieldset", "content" => [
                [ "tag" => "legend", "content" => "Nome" ],
                [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ?? null ] ]
            ]];
        // VALUE
        $contentForm[] = [ 
            "tag" => "fieldset", "content" => [
                [ "tag" => "legend", "content" => "Valor" ],
                [ "tag" => "input", "attributes" => [ "name" => "value", "type" => "text", "value" => $value['value'] ?? null ] ]
            ]];
        // SUBMIT BUTTONS
        $contentForm[] = self::submitButtonSend();
        $contentForm[] = $case == "edit" ? self::submitButtonDelete("/admin/propertyValue/erase") : null;
        // RESPONSE
        return [ "tag" => "form", "attributes" => [ "id" => "form-attributes-$case-$tableHasPart-$idHasPart", "name" => "form-attributes--$case", "action" => "/admin/PropertyValue/$case", "class" => "formPadrao form-propertyValue", "method" => "post" ], "content" => $contentForm ];
    }
}
