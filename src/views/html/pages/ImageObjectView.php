<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Web\Widget\FormTrait;

class ImageObjectView
{
    protected $tableHasPart;
    protected $idHasPart;
    
    use FormTrait;
            
    public function getForm($tableHasPart, $idHasPart, $data = []) 
    {
        $this->tableHasPart = $tableHasPart;
        $this->idHasPart = $idHasPart;
        // form for edit
        $content[] = self::edit($data ?? []);
        // upload
        $content[] = self::upload();
        // save with a database image 
        $content[] = self::addImagesFromDatabase();
        // save with a server image
        $content[] = self::addImagesFromServer();
        return $content;
    }
    
    /**
     * 
     * @param array $data
     * @return array
     */
    public function edit(array $data): array
    {
        if (empty($data)) {
            $content[] = [ "tag" => "p", "content" => "Não há imagens!", "attributes" => [ "class" => "aviso"] ];
            
        } else {
            foreach ($data as $valueEdit) {
                $content[] = self::simpleTag("div", [
                    self::form($valueEdit, $data['isPartOf'] ?? null),
                    self::formIsPartOf($valueEdit)
                ], [ "class" => "box", "style" => "overflow: hidden;"]);
            }              
        }        
        return $content;
    }
    
    private function form($value, $isPartOf = null)
    { 
        $ID = PropertyValue::extractValue($value['identifier'], "id");
        
        if (isset($value['potentialAction'])) {
            foreach ($value['potentialAction'] as $valueAction) {
                $potentialAction[$valueAction['name']] = $valueAction['result'];
            }
        }
        
        $content[] = isset($isPartOf) && $isPartOf['@type'] == "WebPage" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $isPartOf['identifier'] ] ] : null;
        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ] ];
        
        // figure
        $content[] = [ "object" => "figure", "attributes" => [ "style" => "max-width: 200px; float: left; margin-right: 10px;" ], "src" => $value['contentUrl'], "width" => 200 ];
        
        // ID
        $content[] = [ "tag" => "p", "content" => "[ID=$ID] ".$value['contentUrl'] ];
        
        // group
        $content[] = self::fieldsetWithInput(_("Keywords"), "keywords", $value['keywords'], [ "style" => "width: calc(100% - 280px);" ]);        
        
        $content[] = self::submitButtonSend();
        
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "id" => "form-images-edit-{$ID}", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
        
    private function formIsPartOf($value)
    {       
        $ID = PropertyValue::extractValue($value['identifier'], "id");
        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idIsPartOf", "type" => "hidden", "value" => $ID ] ];
        
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'] ?? 1, [ "style" => "width: 80px;" ], "number", [ "min" => "1" ]);
        
        // highlights
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "min-width: 125px; margin: 5px 0;" ], "content" => [
            [ "tag" => "legend", "content" => _("Representative of page") ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 1, ($value['representativeOfPage'] == 1 ? "checked" : null) ] ], _("Yes")
                ] ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 0, $value['representativeOfPage'] == 0 ? "checked" : null ] ], _("No")
                ] ]
            ]
        ];
        
        // caption
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(100% - 435px); margin: 5px 0;" ], "content" => [
            [ "tag" => "legend", "content" => "Legenda" ],
            [ "tag" => "input", "attributes" => [ "name" => "caption", "type" => "text", "value" => $value['caption'] ?? null ] ]
        ]];
        
        if (isset($value['width'])) {
            // width
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 80px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Largura" ],
                [ "tag" => "input", "attributes" => [ "name" => "width", "type" => "text", "value" => $value['width'] ] ]
            ]];
            
            // height
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 80px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Altura" ],
                [ "tag" => "input", "attributes" => [ "name" => "height", "type" => "text", "value" => $value['height'] ] ]
            ]];            
            
            // href
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(100% - 480px); margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Link" ],
                [ "tag" => "input", "attributes" => [ "name" => "href", "type" => "text", "value" => $value['href'] ?? null ] ]
            ]];
        }
        //
        $content[] = self::submitButtonSend();
        $content[] = self::submitButtonDelete("/admin/imageObject/erase");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "id" => "form-images-edit-{$ID}", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
        
    protected function addImagesFromDatabase() 
    {        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "imagesfromdatabase" ] ];
        
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/new", "name" => "imagesFromDatabase", "class" => "formPadrao box", "method" => "post" ], "content" => $content ];
    }
    
    protected function addImagesFromServer($idwebPage = null) 
    {        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = $idwebPage ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $idwebPage ] ] : null;
        
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "imagesfromserver" ] ];        
        
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/insertHasPartFromServer", "name" => "images-selectedFromServer", "id" => "images-selectedFromServer-".$this->idHasPart, "class" => "formPadrao box", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
    
    protected function upload($isPartOf = null) 
    {
        $content[] = [ "tag" => "h4", "content" => "Enviar imagem" ];
        $content[] = $isPartOf ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "value" => $isPartOf['identifier'], "type" => "hidden" ] ] : null;
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "value" => $this->tableHasPart, "type" => "hidden" ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "value" => $this->idHasPart, "type" => "hidden" ] ];
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => "Enviar imagem" ],
            [ "tag" => "input", "attributes" => [ "name" => "imageupload", "type" => "file"] ]
        ] ];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 30%;" ], "content" => [
            [ "tag" => "legend", "content" => "Salvar na pasta" ],
            [ "tag" => "input", "attributes" => [ "name" => "location", "type" => "text", "list" => "textlocation"] ]
        ] ];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 10%;" ], "content" => [
            [ "tag" => "legend", "content" => _("Keywords") ],
            [ "tag" => "input", "attributes" => [ "name" => "keywords", "type" => "text", "list" => "groups" ] ]
        ] ];
        $content[] = self::submitButtonSend();
        
        return [ "tag" => "form", "attributes" => [ "name" => "form-images-upload", "id" => "form-images-uploadImage-".$this->idHasPart, "action" => '/admin/imageObject/new', "class" => "box formPadrao", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ]; 
    }
}
