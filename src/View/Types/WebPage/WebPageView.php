<?php
namespace Plinct\Cms\View\Types\WebPage;

use Plinct\Cms\View\Types\Intangible\PropertyValueView;
use Plinct\Cms\View\Types\WebPageElement\WebPageElementView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\SitemapWidget;
use Plinct\Tool\ArrayTool;

class WebPageView extends WebPageWidget {
    private $content;

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
        // VARS
        parent::$idwebSite = ArrayTool::searchByValue($value['isPartOf']['identifier'],'id','value');
        parent::$idwebPage = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        // VIEW         
        $this->content['main'][] = [ "tag" => "p", "content" => _("View")." <a href=\"".$value['url']."\" target=\"_blank\">".$value['url']."</a>" ];
        // EDIT
        $content['main'][] = self::formWebPage($value);
        // ATTRIBUTES
        $content['main'][] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPage", parent::$idwebPage, $value['identifier']) ]);
        // BOX
        $this->content['main'][] = self::divBox($value['name'], "WebPage", [ $content ]);
        // WEB ELEMENTS
        $this->content['main'][] = self::divBoxExpanding(_("Web elements"), "WebPage", [ (new WebPageElementView())->getForm(parent::$idwebPage, $value['hasPart']) ]);
        return $this->content;
    }

    public static function editWithIsPartOf(array $data): array {
        // VARS
        parent::$idwebSite = ArrayTool::searchByValue($data['isPartOf']['identifier'],'id','value');
        parent::$idwebPage = ArrayTool::searchByValue($data['identifier'], "id")['value'];
        // FORM EDIT and PROPERTIES
        $response[] = self::divBox2(_("Edit web page"), [
            self::editWebPage($data),
            self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPage", parent::$idwebPage, $data['identifier']) ])
        ]);
        // WEB ELEMENTS
        $response[] = self::divBoxExpanding(_("Web page elements"), "WebPage", [ (new WebPageElementView())->getForm(parent::$idwebPage, $data['hasPart']) ]);
        // RESPONSE
        return $response;
    }

    public function sitemap($sitemaps): array {
        // TITLE
        $this->content['main'][] = self::simpleTag("h2",_("Sitemaps"));
        // INDEX
        $this->content['main'][] = (new SitemapWidget())->index($sitemaps);
        return $this->content;
    }
}
