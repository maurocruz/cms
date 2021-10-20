<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\WebPage;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\Intangible\PropertyValueView;
use Plinct\Cms\View\Types\WebPageElement\WebPageElementView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\SitemapWidget;
use Plinct\Tool\ArrayTool;

class WebPageView extends WebPageAbstract
{
    /**
     *
     */
    private static function navbarWebPage(string $title = null)
    {
        View::navbar("WebPage",[
            "/admin/webSite/webPage?id=".self::$idwebSite => Fragment::icon()->home(),
            "/admin/webSite/webPage?id=".self::$idwebSite."&action=new" => Fragment::icon()->plus(),
            "/admin/webSite/webPage?id=".self::$idwebSite."&action=sitemap" => _("Site map")
        ], 4, ['table'=>"webPage"]);

        if($title) View::navbar(_($title), [], 5);
    }

    /**
     * @param array $data
     */
    public static function index(array $data)
    {
        // FROM WEBSITE CONTROLLER
        if ($data['@type'] == 'WebSite') {
            self::$idwebSite = ArrayTool::searchByValue($data['identifier'], 'id', 'value');

            self::navbarWebPage();

            if (isset($data['error'])) {
                View::main(Fragment::miscellaneous()->message($data['error']));

            } elseif (isset($data['hasPart'])) {
                View::main(parent::listAllWebPages($data['hasPart']));
            }
        }
    }


    /**
     * @param array $data
     */
    public static function new(array $data)
    {
        // VARS
        self::$idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');

        // NAVBAR
        self::navbarWebPage("Add new webpage");

        // FORM
        View::main(Fragment::box()->simpleBox(self::formWebPage(), _("Add new webpage")));
    }

    /**
     * @throws Exception
     */
    public static function edit(array $data)
    {
        // VARS
        parent::$idwebSite = ArrayTool::searchByValue($data['isPartOf']['identifier'],'id','value');
        parent::$idwebPage = ArrayTool::searchByValue($data['identifier'], "id", 'value');

        self::navbarWebPage();

        // FORM EDIT
        View::main(Fragment::box()->simpleBox(self::formWebPage($data), ("Edit")));

        // PROPERTIES
        View::main(Fragment::box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage", parent::$idwebPage, $data['identifier'])));

        // WEB ELEMENTS
        View::main(Fragment::box()->expandingBox(_("Web page elements"), (new WebPageElementView())->getForm((int)parent::$idwebPage, $data['hasPart'])));
    }

    /**
     * @param $data
     */
    public static function sitemap($data)
    {
        parent::$idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');

        self::navbarWebPage(_("Sitemaps"));

        // TITLE
        View::main("<h2>"._("Sitemaps")."</h2>");

        // INDEX
        View::main((new SitemapWidget())->index($data['sitemaps']));
    }
}
