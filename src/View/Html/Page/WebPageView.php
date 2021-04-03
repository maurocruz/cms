<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Html\Widget\SitemapWidget;
use Plinct\Tool\ArrayTool;

class WebPageView extends AbstractView {
    protected $idwebPage;

    use FormElementsTrait;
    use HtmlPiecesTrait;
    
    public function __construct() {
        $appendNavbar = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "webPage", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = [
            "list" => [ "/admin/webPage" => "Show all WebPages", "/admin/webPage/new" => "add new WebPage", "/admin/webPage/sitemap" => _("Site map") ],
            "attributes" => [ "class" => "menu menu3"],
            "title" => "WebPage",
            "append" => $appendNavbar
        ];  
    }
    
    public function index(array $data): array {
        if (isset($data['error'])) {
            $this->content['main'][] = self::error($data['error'], "WebPage");
        } else {
            $this->content['main'][] = self::listAll($data, "WebPage", "List of webpages", [ "url" => "Url", "dateModified" => "Date modified" ]);
        }
        return $this->content;
    }
    
    public function new(): array {
        $content[] = [ "tag" => "h4", "content" => "Add new webPage" ];
        $content[] = self::formWebPage();
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
        $this->idwebPage = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        // VIEW         
        $this->content['main'][] = [ "tag" => "p", "content" => _("View")." <a href=\"".$value['url']."\" target=\"_blank\">".$value['url']."</a>" ];
        // EDIT
        $content['main'][] = self::formWebPage("edit", $value);
        // ATTRIBUTES
        $content['main'][] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPage", $this->idwebPage, $value['identifier']) ]);
        // BOX
        $this->content['main'][] = self::divBox($value['name'], "WebPage", [ $content ]);
        // WEB ELEMENTS
        $this->content['main'][] = self::divBoxExpanding(_("Web elements"), "WebPage", [ (new WebPageElementView())->getForm($this->idwebPage, $value['hasPart']) ]);
        return $this->content;
    }

    public function sitemap($sitemaps): array {
        // TITLE
        parent::addMain(self::simpleTag("h2",_("Sitemaps")));
        // INDEX
        parent::addMain((new SitemapWidget())->index($sitemaps));
        return $this->content;
    }

    private function formWebPage($case = "new", $value = null): array {
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $this->idwebPage, "type" => "hidden" ] ] : null;
        // title
        $content[] = self::fieldsetWithInput("Título", "name", $value['name'] ?? null , [ "style" => "width: 50%;"]);
        // url
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 50%"], "content" => [
                [ "tag" => "legend", "content" => "Url" ],
                [ "tag" => "input", "attributes" => [ "name" => "url", "type" => "text", "value" => $value['url'] ?? null ] ]
            ]];
        // description
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;"], "content" => [
                [ "tag" => "legend", "content" => "Descrição (não usar html)" ],
                [ "tag" => "textarea", "attributes" => [ "style" => "height: auto; width: 100%;", "name" => "description" ], "content" => $value['description'] ?? null  ]
            ]];
        // alternativeHeadline
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 400px"], "content" => [
                [ "tag" => "legend", "content" => "alternativeHeadline" ],
                [ "tag" => "input", "attributes" => [ "name" => "alternativeHeadline", "type" => "text", "value" => $value['alternativeHeadline'] ?? null  ] ]
            ]];
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/webPage/erase") : null;
        return [ "tag" => "form", "attributes" => [ "id" => "form-pages--edit", "name" => "form-pages--edit", "action" => "/admin/webPage/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];
    }
}
