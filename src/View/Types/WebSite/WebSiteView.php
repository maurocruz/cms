<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\WebSite;

use Exception;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\Types\WebPage\WebPageView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Tool\ArrayTool;

class WebSiteView extends WebSiteAbstract
{
    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarWebSite();

        $rowColumns = [
            'url'=>'Url'
        ];

        MainView::content(HtmlPiecesTrait::listAll($data,'webSite',_("List of websites"), $rowColumns));
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        // NAVBAR
        $this->navbarWebSite();

        // FORM
        MainView::content(self::newView());
    }

    /**
     * @param array $data
     */
    public function edit(array $data)
    {
        $value = $data[0];
        $this->idwebSite = ArrayTool::searchByValue($value['identifier'],'id','value');

        // navbar
        parent::navbarWebSite($value['name']);

        // form
        MainView::content(parent::editView($value));

        // list web pages
        WebPageView::index($value);
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function webPage(array $data)
    {
        // ITEM
        if ($data['@type'] == "WebPage") {
            $this->idwebSite = ArrayTool::searchByValue($data['isPartOf']['identifier'],'id','value');

            parent::navbarWebSite($data['isPartOf']['name']);

            WebPageView::edit($data);

        } else {
            $this->idwebSite = ArrayTool::searchByValue($data['identifier'], 'id', 'value');

            // navbar
            parent::navbarWebSite($data['name']);

            if (isset($data['hasPart'])) {
                // LIST ALL
                WebPageView::index($data);

            } elseif(isset($data['sitemaps'])) {
                WebPageView::sitemap($data);
            } else {
                // NEW WEB PAGE
                WebPageView::new($data);
            }
        }
    }
}
