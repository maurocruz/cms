<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\WebPage;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\WebSite\Type\WebPageElement\WebPageElementView;
use Plinct\Tool\ArrayTool;

class WebPageView extends WebPageAbstract
{
  /**
   *
   */
  private static function navbarWebPage(string $title = null)
  {
    if (self::$idwebSite) {
      CmsFactory::webSite()->addHeader(
        CmsFactory::response()->fragment()->navbar()
          ->type('webPage')
          ->level(4)
          ->title("WebPage")
          ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite, CmsFactory::response()->fragment()->icon()->home())
          ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=new", CmsFactory::response()->fragment()->icon()->plus())
          ->newTab("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=sitemap", _("Site map"))
          ->search("/admin/webSite/webPage?id=" . self::$idwebSite . "&action=search", 'name', null, '/admin/webSite/webPage?id=' . self::$idwebSite . '&item=[idItem]')
          ->ready()
      );
    } else {
      CmsFactory::webSite()->addHeader(
				CmsFactory::response()->fragment()->navbar()
          ->type('webPage')
          ->level(2)
          ->title("WebPage")
          ->newTab("/admin/webPage", CmsFactory::response()->fragment()->icon()->home())
          ->newTab("/admin/webPage/new", CmsFactory::response()->fragment()->icon()->plus())
          ->search("/admin/webPage/search")
          ->ready()
        );
    }

    if ($title) CmsFactory::webSite()->addHeader(
      CmsFactory::response()->fragment()->navbar()
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
      self::$idwebSite = $data['idwebSite'];
      self::navbarWebPage();
      if (isset($data['error'])) {
        CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->message($data['error']));
      } elseif (isset($data['hasPart'])) {
        $list = isset($data['hasPart']['@type']) && $data['hasPart']['@type'] == 'ItemList' ? $data['hasPart']['itemListElement'] : $data['hasPart'];
        CmsFactory::webSite()->addMain(
          CmsFactory::response()->fragment()->listTable(['class'=>'table'])
            ->caption(_("List of webpages"))
            ->labels("id",_("Name"),"url",_("Date modified"))
            ->setEditButton("/admin/webSite/webPage?id=".self::$idwebSite."&item=")
            ->rows($list,['idwebPage','name','url','dateModified'])
            ->ready()
        );
      }
    } else {
      self::navbarWebPage();
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->listTable()
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
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formWebPage(), _("Add new webpage")));
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
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit")));
    // PROPERTIES
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage", parent::$idwebPage, $data['identifier'])));
    // WEB ELEMENTS
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Web page elements"), (new WebPageElementView())->getForm(parent::$idwebPage, $data['hasPart'])));
  }
  /**
   * @param $data
   */
  public static function sitemap($data)
  {
    parent::$idwebSite = $data['idwebSite'];
    self::navbarWebPage(_("Sitemaps"));
    // TITLE
    CmsFactory::webSite()->addMain("<h2>"._("Sitemaps")."</h2>");
    // INDEX
		/*$form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-sitemaps']);
		$form->action('/admin/sitemap/new')->method('post');
		$form->fieldsetWithInput('urlForData', null, 'Url for data api');
		$form->fieldsetWithInput('loc', null, 'loc url');
		$form->fieldsetWithInput('lastmod', null, 'Lastmod property');
		$form->submitButtonSend();
		CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox('Adicionar novo sitemap', $form->ready()));*/
		//
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->sitemap($data['sitemaps']));
  }
}
