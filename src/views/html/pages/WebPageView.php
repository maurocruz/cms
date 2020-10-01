<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Piece\HtmlPiecesTrait;
use Plinct\Web\Widget\FormTrait;

class WebPageView
{
    protected $content;
    protected $idwebPage;

    use FormTrait;
    use HtmlPiecesTrait;
    
    public function __construct()
    {
        $appendNavbar = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "webPage", "data-searchfor" => "name" ] ];

        $this->content['navbar'][] = [
            "list" => [ "/admin/webPage" => "Show all WebPages", "/admin/webPage/new" => "add new WebPage" ],
            "attributes" => [ "class" => "menu menu3"],
            "title" => "WebPage",
            "append" => $appendNavbar
        ];  
    }
    
    public function index(array $data): array 
    {
        if (isset($data['error'])) {
            $this->content['main'][] = self::error($data['error'], "WebPage");
        } else {
            //$this->content['main'][] = (new DirectoryTree($data))->view();

            $this->content['main'][] = self::listAll($data, "WebPage", "List of webpages", [ "url" => "Url", "dateModified" => "Date modified" ]);
        }
        
        return $this->content;
    }
    
    public function new(): array
    {
        $content[] = [ "tag" => "h4", "content" => "Add new webPage" ];
        $content[] = self::form();
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        return $this->content;
    }

    public function edit(array $data): array
    {
        $value = $data[0];

        $this->idwebPage = PropertyValue::extractValue($value['identifier'], "id");
        
        // VIEW         
        $this->content['main'][] = [ "tag" => "p", "content" => _("View")." <a href=\"".$value['url']."\" target=\"_blank\">".$value['url']."</a>" ];
        
        // EDIT
        $content['main'][] = self::form("edit", $value);
        
        // ATTRIBUTES
        $content['main'][] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPage", $this->idwebPage, $value['identifier']) ]);

        // BOX
        $this->content['main'][] = self::divBox($value['name'], "WebPage", [ $content ]);
        
        // WEB ELEMENTS
        $this->content['main'][] = self::divBoxExpanding(_("Web elements"), "WebPage", [ (new WebPageElementView())->getForm($this->idwebPage, $value['hasPart']) ]);
        
        return $this->content;
    }
    
    private function form($case = "new", $value = null) 
    {        
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $this->idwebPage, "type" => "hidden" ] ] : null;
        
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
        
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/webPage/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "id" => "form-pages--edit", "name" => "form-pages--edit", "action" => "/admin/webPage/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];
    }
}
