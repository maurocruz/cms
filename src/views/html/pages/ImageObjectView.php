<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\App;
use Plinct\Api\Type\PropertyValue;

class ImageObjectView
{
    protected $tableOwner;
    protected $idOwner;
    
    use \Plinct\Web\Widget\FormTrait;    
            
    public function getForm($tableOwner, $idOwner, $data = []) 
    {
        $this->tableOwner = $tableOwner;
        $this->idOwner = $idOwner;
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
     * @param type $data
     * @param type $mode
     * @return array
     */
    public function edit(array $data): array
    {
        if (empty($data)) {
            $content[] = [ "tag" => "p", "content" => "Não há imagens!", "attributes" => [ "class" => "aviso"] ];
        } else {
            foreach ($data as $valueEdit) {
                $content[] = self::form($valueEdit, $data['isPartOf'] ?? null);
            }              
        }        
        return $content;
    }
    
    private function form($value, $isPartOf = null, $mode = "simple") 
    { 
        $ID = PropertyValue::extractValue($value['identifier'], "fwc_id");
        if (isset($value['potentialAction'])) {
            foreach ($value['potentialAction'] as $valueAction) {
                $potentialAction[$valueAction['name']] = $valueAction['result'];
            };
        }
        
        $content[] = isset($isPartOf) && $isPartOf['@type'] == "WebPage" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $isPartOf['identifier'] ] ] : null;
        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "type" => "hidden", "value" => $this->tableOwner ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "type" => "hidden", "value" => $this->idOwner ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idimageObject", "type" => "hidden", "value" => $ID ] ];
        
        // figure
        $content[] = [ "object" => "figure", "attributes" => [ "style" => "max-width: 200px; float: left; margin-right: 10px;" ], "src" => $value['contentUrl'], "width" => 200 ];
        // ID
        $content[] = [ "tag" => "p", "content" => "[ID=$ID] ".$value['contentUrl'] ];
        // group
        $content[] = self::fieldsetWithInput(_("Keywords"), "keywords", $value['keywords'], [ "style" => "width: 240px;" ]);
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'], [ "style" => "width: 80px;" ], "number", [ "min" => "1" ]);
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
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 700px; margin 5px 0;" ], "content" => [
            [ "tag" => "legend", "content" => "Legenda" ],
            [ "tag" => "input", "attributes" => [ "name" => "caption", "type" => "text", "value" => $value['caption'] ?? null ] ]
        ]]; 
        if ($mode == "complete") {
            $content[] = "<br>";
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
            // incontent
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "min-width: 125px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Colocar acima do texto?" ],
                [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                    [ "tag" => "input",  "attributes" => [ "name" => "incontent", "type" => "radio", "value" => 1, ($potentialAction['inContent'] == 1 ? "checked" : null) ] ], "Sim"
                    ] ],
                [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                    [ "tag" => "input",  "attributes" => [ "name" => "incontent", "type" => "radio", "value" => 0, ($potentialAction['inContent'] == 0 ? "checked" : null) ] ], "Não"
                    ] ]
                ]
            ];
            $content[] = "<br>";
            // href
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 540px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Link" ],
                [ "tag" => "input", "attributes" => [ "name" => "href", "type" => "text", "value" => $potentialAction['href'] ?? null ] ]
            ]];
        }
        //
        $content[] = self::submitButtonSend([ "onclick" => "return submitFromAjax(this,'form-images-edit-$ID');" ]);
        $content[] = self::submitButtonDelete("/admin/imageObject/deleteHasPart");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "box formPadrao", "style" => "overflow: hidden;", "id" => "form-images-edit-{$ID}", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
        
    protected function addImagesFromDatabase() 
    {        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "type" => "hidden", "value" => $this->tableOwner ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "type" => "hidden", "value" => $this->idOwner ] ];
        
        $content[] = [ "tag" => "div", "attributes" => [ "id" => "imagesfromdatabase", "class" => "imagesFromDatabase" ] ];
        
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/postRelationship", "name" => "imagesFromDatabase", "class" => "formPadrao box", "method" => "post" ], "content" => $content ];
    }
    
    protected function addImagesFromServer($idwebPage = null) 
    {        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "type" => "hidden", "value" => $this->tableOwner ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "type" => "hidden", "value" => $this->idOwner ] ];
        $content[] = $idwebPage ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $idwebPage ] ] : null;
        
        $content[] = [ "tag" => "div", "attributes" => [ "id" => "imagesfromserver" ] ];        
        
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/insertHasPartFromServer", "name" => "images-selectedFromServer", "id" => "images-selectedFromServer-".$this->idOwner, "class" => "formPadrao box", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
    
    protected function upload($isPartOf = null) 
    {
        $content[] = [ "tag" => "h4", "content" => "Enviar imagem" ];
        $content[] = $isPartOf ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "value" => $isPartOf['identifier'], "type" => "hidden" ] ] : null;
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "value" => $this->tableOwner, "type" => "hidden" ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "value" => $this->idOwner, "type" => "hidden" ] ];
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
        
        return [ "tag" => "form", "attributes" => [ "name" => "form-images-upload", "id" => "form-images-uploadImage-".$this->idOwner, "action" => '/admin/ImageObject/addWithPartOf', "class" => "box formPadrao", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ]; 
    }
}
