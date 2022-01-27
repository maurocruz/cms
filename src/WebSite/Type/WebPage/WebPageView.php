<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebPage;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\WebSite\Type\WebPageElement\WebPageElementView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Cms\WebSite\WebSite;
use Plinct\Tool\ArrayTool;

class WebPageView extends WebPageAbstract
{
    /**
     *
     */
    private static function navbarWebPage(string $title = null)
    {
        if (self::$idwebSite) {
            WebSite::addHeader(
                Fragment::navbar()
                    ->type('webPage')
                    ->level(4)
                    ->title("WebPage")
                    ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite, Fragment::icon()->home())
                    ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=new", Fragment::icon()->plus())
                    ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=sitemap", _("Site map"))
                    ->search("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=search", 'name', null, '/admin/webSite/webPage?id=' . self::$idwebSite . '&item=[idItem]')
                    ->ready()
            );
        } else {
            View::contentHeader(Fragment::navbar()
                ->type('webPage')
                ->level(2)
                ->title("WebPage")
                ->newTab("/admin/webPage", Fragment::icon()->home())
                ->newTab("/admin/webPage/new", Fragment::icon()->plus())
                ->search("/admin/webPage/search")
                ->ready()
            );
        }

        if ($title) WebSite::addHeader(
            Fragment::navbar()
                ->level(5)
                ->title($title)
                ->ready()
        );
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
                $list = isset($data['hasPart']['@type']) && $data['hasPart']['@type'] == 'ItemList' ? $data['hasPart']['itemListElement'] : $data['hasPart'];
                View::main(
                    Fragment::listTable(['class'=>'table'])
                        ->caption(_("List of webpages"))
                        ->labels("id",_("Name"),"url",_("Date modified"))
                        ->setEditButton("/admin/webSite/webPage?id=".self::$idwebSite."&item=")
                        ->rows($list,['idwebPage','name','url','dateModified'])
                        ->ready()
                );
            }
        } else {
            self::navbarWebPage();
            View::main(Fragment::listTable()
                ->caption("WebPages")
                ->labels('id', _('Name'), "Url", _("Date modified"))
                ->rows($data['itemListElement'],['idwebPage','name','url','dateModified'])
                ->setEditButton("/admin/webPage/edit/")
                ->ready()
            );
        }
    }

    /**
     * * * * * NEW * * * * *
     *
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
     * * * * * EDIT * * * * *
     * @throws Exception
     */
    public static function edit(array $data)
    {
        // VARS
        parent::$idwebSite = ArrayTool::searchByValue($data['isPartOf']['identifier'],'id','value');
        parent::$idwebPage = ArrayTool::searchByValue($data['identifier'], "id", 'value');

        self::navbarWebPage($data['name']);

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
        View::main(Fragment::miscellaneous()->sitemap($data['sitemaps']));
    }
}
