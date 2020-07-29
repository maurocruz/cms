<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class WebPageElementView
{
    protected $idwebPage;
    protected $idwebPageElement;
    
    use \Plinct\Web\Widget\FormTrait;
    
    public function getForm($tableOwner, $idOwner, $value) 
    {
        $this->idwebPage = $idOwner;
        
        // add new WebPagEelement
        $content[] = self::divBoxExpanding(_("Add new"), "WebPageelement", [ self::form() ]);
        
        // WebPageElements hasPart
        if ($value['numberOfItems'] > 0) {
            foreach ($value['itemListElement'] as $valuePart) {
                $item = $valuePart['item'];
                $title = $item['name'] !== ''? $item['name'] : _("[No title]");
                $content[] = self::divBoxExpanding($title, "WebPageElement", [ self::edit($item) ]);
            }
        }
        
        return $content;
    }
    
    public function index(array $data): array
    {

    }
       
    public function new($data = null): array
    {   
        $content[] = [ "tag" => "h4", "content" => "Adicionar novo <span class=\"box-expanding--text\">[<a href=\"javascript: void(0)\" onclick=\"expandBox(this,'box-WebPageElement-add');\">Expandir</a>]</span>" ];
        $content[] = self::form();        
        return [ "tag" => "div", "attributes" => [ "id" => "box-WebPageElement-add", "class" => "box box-expanding" ], "content" => $content ];
        return $content;
    } 
    
    public function edit(array $value): array
    {
        $this->idwebPageElement = PropertyValue::extractValue($value['identifier'], "ID");       
        // content       
        $content[] = self::form("edit", $value);
        // attributes
        $content[] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPageElement", $this->idwebPageElement, $value) ]);
        // images
        $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("webPageElement", $this->idwebPageElement, $value['image']) ]);
        return $content;
    }
    
    
    protected function boxContent($value) {
        $contentForm[] = [ "tag" => "h4", "content" => "Content <span class=\"box-expanding--text\">[<a href=\"javascript: void(0)\" onclick=\"expandBox(this,'form-webPageElement--edit-$this->idwebPageElement');\">Expandir</a>]</span>" ];
        $contentForm[] = self::form("edit", $value);                
        return [ "tag" => "div", "attributes" => [ "id" => "form-webPageElement--edit-$this->idwebPageElement", "class" => "box box-expanding" ], "content" => $contentForm ];
    }

    protected function form($case = "add", $value = null) 
    {
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableOwner", "value" => "webPage", "type" => "hidden" ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idOwner", "value" => $this->idwebPage, "type" => "hidden" ] ];        
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPageElement", "value" => $this->idwebPageElement, "type" => "hidden" ] ] : null;
                             
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
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/webPageElement/delete") : null;
                
        return [ "tag" => "form", "attributes" => [ "name" => "form-webPageElement--$case", "id" => "form-webPageElement-$case-$this->idwebPageElement", "action" => "/admin/webPageElement/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];                           
    }
}
