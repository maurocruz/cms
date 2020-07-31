<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class WebPageView
{
    protected $content;
    protected $idwebPage;

    use \Plinct\Web\Widget\FormTrait;
    
    public function __construct()
    {     
        $this->content['navbar'][] = [
            "list" => [ "/admin/webPage" => "Show all WebPages", "/admin/webPage/add" => "add new WebPage" ],
            "attributes" => [ "class" => "menu menu3"],
            "title" => "WebPage"
        ];  
    }
    
    public function index(array $data): array 
    {            
        if (isset($data['errorInfo'])) {
            $this->content['main'][] = self::errorInfo($data['errorInfo'], "WebPage");
        } else {
            $this->content['main'][] = self::listAll($data, "WebPage", "List of webpages", [ "dateModified" => "Date modified" ]);
        }
        
        return $this->content;
    }
    
    public function new($data = null): array
    {
        $content[] = [ "tag" => "h4", "content" => "Add new webPage" ];
        $content[] = self::form();
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        return $this->content;
    }

    public function edit(array $data): array
    {
        $value = $data[0];
        
        //$valueWebPageElement = $data['webPageElement'];
        
        $this->idwebPage = PropertyValue::extractValue($value['identifier'], "id");
        
        // VIEW         
        $this->content['main'][] = [ "tag" => "p", "content" => _("View")." <a href=\"".$value['url']."\" target=\"_blank\">".$value['url']."</a>" ];
        
        // EDIT
        $this->content['main'][] = self::divBox("Webpage", "WebPage", [ self::form("edit", $value) ]);
        
        // ATTRIBUTES
        //$content[] = self::divBoxExpanding(_("Properties"), "WebPage", [ (new PropertyValueView())->getForm("pages", $this->idwebPage, $value['propertyValue']) ]);
        
        // BOX 
       // $this->content['main'][] = self::divBox($value['name'], "WebPage", [ $content ]);
        
        // WEB ELEMENTS
        $this->content['main'][] = self::divBoxExpanding(_("Web elements"), "WebPage", [ (new WebPageElementView())->getForm("pages", $this->idwebPage, $value['hasPart']) ]);
        
        return $this->content;
    }
    
    private function form($case = "new", $value = null) 
    {        
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "value" => $this->idwebPage, "type" => "hidden" ] ] : null;
        
        // title
        $content[] = self::fieldsetWithInput("Título", "name", $value['name'], [ "style" => "width: calc(100% - 400px);"]);
        
        // url
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 400px"], "content" => [
                [ "tag" => "legend", "content" => "Url" ],
                [ "tag" => "input", "attributes" => [ "name" => "url", "type" => "text", "value" => str_replace("//".$_SERVER['HTTP_HOST'], "", $value['url']) ] ]
            ]];
        
        // description
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;"], "content" => [
                [ "tag" => "legend", "content" => "Descrição (não usar html)" ],
                [ "tag" => "textarea", "attributes" => [ "style" => "height: auto; width: 100%;", "name" => "description" ], "content" => $value['description'] ]
            ]];
        
        // alternativeHeadline
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 400px"], "content" => [
                [ "tag" => "legend", "content" => "alternativeHeadline" ],
                [ "tag" => "input", "attributes" => [ "name" => "alternativeHeadline", "type" => "text", "value" => $value['alternativeHeadline'] ] ]
            ]];
        
        // show title
        //$content[] = self::radio("Mostrar título", "showtitle", $value['showtitle'], [ 1, 0 ] );        
        //$content[] = self::radio("Mostrar descrição", "showdescription", $value['showdescription'], [ 1, 0 ] );
        
        $content[] = self::submitButtonSend();
        
        $content[] = self::submitButtonDelete("/admin/webPage/erase");
        
        return [ "tag" => "form", "attributes" => [ "id" => "form-pages--edit", "name" => "form-pages--edit", "action" => "/admin/webPage/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];
    }
}
