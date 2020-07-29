<?php

namespace Plinct\Cms\View\Html\Page;



class PropertyValueView
{
    protected $idWebPage;
    
    use \Plinct\Web\Widget\FormTrait;
    
    public function getForm($tableOwner, $idOwner, $value) 
    {     
        $this->idWebPage = $value['isPartOf']['identifier'];
        
        $idBox = "box-attributes-$tableOwner-$idOwner";  
        
        if (isset($value['cssSelector'])) {
            foreach ($value['cssSelector'] as $value) {
                $content[] = self::form($tableOwner, $idOwner, 'edit', $value);
            }
        } 
        
        // new
        $content[] = self::form($tableOwner, $idOwner);  
        
        return $content;
    }
    
    protected function form($tableOwner, $idOwner, $case = "add", $value = null)
    {      
        if ($case == "add") {
            $contentForm[] = "Novo: ";
        } else {
            $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "idattributes", "type" => "hidden", "value" => $value['idattributes'] ] ];
        }
        $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $this->idWebPage ] ];
        $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "type" => "hidden", "value" => $tableOwner ] ];
        $contentForm[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "type" => "hidden", "value" => $idOwner ] ];
        $contentForm[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 15%;" ], "content" => [
                [ "tag" => "legend", "content" => "Nome" ],
                [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ?? null ] ]
            ]];        
        $contentForm[] = [ 
            "tag" => "fieldset", "attributes" => [ "style" => "width: 50%;" ], "content" => [
                [ "tag" => "legend", "content" => "Valor" ],
                [ "tag" => "input", "attributes" => [ "name" => "value", "type" => "text", "value" => $value['value'] ?? null ] ]
            ]];
        
        $contentForm[] = self::submitButtonSend();
        
        $contentForm[] = $case == "edit" ? self::submitButtonDelete("/admin/propertyValue/delete") : null;
                
        return [ "tag" => "form", "attributes" => [ "id" => "form-attributes-$case-$tableOwner-$idOwner", "name" => "form-attributes--$case", "action" => "/admin/PropertyValue/$case", "class" => "formPadrao", "method" => "post" ], "content" => $contentForm ];
    }
}
