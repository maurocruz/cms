<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Web\Widget\FormTrait;

class WebPageElementView
{
    protected $idwebPage;
    
    protected $idwebPageElement;
    
    use FormTrait;
    
    public function getForm($idHasPart, $value)
    {
        $this->idwebPage = $idHasPart;
        
        // add new WebPagElement
        $content[] = self::divBoxExpanding(_("Add new"), "WebPageElement", [ self::form() ]);
        
        // WebPageElements hasPart
        foreach ($value as $valueWebPageElement) {
            $this->idwebPageElement = PropertyValue::extractValue($valueWebPageElement['identifier'], "id");
            
            $content[] = self::divBoxExpanding("[".$this->idwebPageElement."] ".$valueWebPageElement['name'], "WebPageElement", [ self::edit($valueWebPageElement) ]);
        }
        
        return $content;
    }
    
    public function new(): array
    {   
        $content[] = [ "tag" => "h4", "content" => "Adicionar novo <span class=\"box-expanding--text\">[<a href=\"javascript: void(0)\" onclick=\"expandBox(this,'box-WebPageElement-add');\">Expandir</a>]</span>" ];
        $content[] = self::form();        
        return [ "tag" => "div", "attributes" => [ "id" => "box-WebPageElement-add", "class" => "box box-expanding" ], "content" => $content ];
    } 
    
    public function edit(array $value): array
    {        
        // content       
        $content[] = self::form("edit", $value);
        
        // attributes
        $content[] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPageElement", $this->idwebPageElement, $value['identifier']) ]);
        //
        // images
        $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("webPageElement", $this->idwebPageElement, $value['image']) ]);
        
        return $content;
    }

    protected function form($case = "add", $value = null) 
    {
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $this->idwebPageElement, "type" => "hidden" ] ] : null;
                             
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 93%;" ], "content" => [
                [ "tag" => "legend", "content" => "Título" ],
                [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ] ]
            ]];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 6%;" ], "content" => [
                [ "tag" => "legend", "content" => "Posição" ],
                [ "tag" => "input", "attributes" => [ "name" => "position", "type" => "text", "value" => $value['position'] ] ]
            ]];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => "Conteúdo (usar HTML)" ],
                [ "tag" => "textarea", "attributes" => [ "id" => "textareaPost-$this->idwebPageElement", "style" => "width: 100%;", "name" => "text" ], "content" => $value['text'] ]
            ]];            
        $content[] = [ "tag" => "a", "attributes" => [ "href" => "javascript:void();", "onclick" => "expandTextarea('textareaPost-$this->idwebPageElement',100);", "style" => "width: 96%; display: block;" ], "content" => "Expandir textarea em 100px" ];              
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/webPageElement/erase") : null;
                
        return [ "tag" => "form", "attributes" => [ "name" => "form-webPageElement--$case", "id" => "form-webPageElement-$case-$this->idwebPageElement", "action" => "/admin/webPageElement/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];                           
    }
}
