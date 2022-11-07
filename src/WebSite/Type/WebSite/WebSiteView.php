<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebSite;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\WebPage\WebPageView;
use Plinct\Tool\ArrayTool;

class WebSiteView extends WebSiteAbstract
{
  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarWebSite();

    $listTable = CmsFactory::response()->fragment()->listTable();
    $listTable->caption(_("List of websites"))
      ->labels("url")
      ->rows($data['itemListElement'],['url'])
      ->setEditButton("/admin/webSite/edit/");

		CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   */
  public function new()
{
    // NAVBAR
    $this->navbarWebSite();
    // FORM
    CmsFactory::webSite()->addMain(self::newView());
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
    CmsFactory::webSite()->addMain(parent::editView($value));

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
