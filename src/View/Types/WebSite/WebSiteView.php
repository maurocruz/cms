<?php
namespace Plinct\Cms\View\Types\WebSite;

use Plinct\Cms\View\Types\WebPage\WebPageView;
use Plinct\Cms\View\Types\WebPage\WebPageWidget;
use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Tool\ArrayTool;

class WebSiteView extends WebSiteWidget implements ViewInterface {

    public function index(array $data): array {
        $this->navbarWebSite();
        $rowColumns = [
            'url'=>'Url'
        ];
        $this->content['main'][] = HtmlPiecesTrait::listAll($data,'webSite',_("List of websites"), $rowColumns);
        return $this->content;
    }

    public function new($data = null): array {
        // NAVBAR
        $this->navbarWebSite();
        // FORM
        $this->content['main'][] = self::newView();
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
        $this->idwebSite = ArrayTool::searchByValue($value['identifier'],'id','value');
        // navbar
        parent::navbarWebSite($value['name']);
        // form
        $this->content['main'][] = parent::editView($value);
        // list web pages
        $this->content['main'][] = parent::listAllWebPages($value);
        return $this->content;
    }

    /**
     * @param array $data
     * @return array
     */
    public function webPage(array $data): array {
        // ITEM
        if ($data['@type'] == "WebPage") {
            $this->idwebSite = ArrayTool::searchByValue($data['isPartOf']['identifier'],'id','value');
            parent::navbarWebSite($data['isPartOf']['name']);
            $this->content['main'][] = WebPageView::editWithIsPartOf($data);
        } else {
            $this->idwebSite = ArrayTool::searchByValue($data['identifier'], 'id', 'value');
            // navbar
            parent::navbarWebSite($data['name']);
            if (isset($data['hasPart'])) {
                // LIST ALL
                $this->content['main'][] = parent::listAllWebPages($data);
            } else {
                // NEW WEB PAGE
                $this->content['main'][] = self::divBox2(_('New web page'), [WebPageWidget::newWebPage($data)]);
            }
        }
        return $this->content;
    }
}