<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\WebSite;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\WebSite\Type\WebPage\WebPageView;
use Plinct\Tool\ArrayTool;

class WebSiteView extends WebSiteAbstract
{
	/**
	 */
  public function index()
  {
    $this->navbarWebSite();
		CmsFactory::webSite()->addMain("
			<div 
				class='plinct-shell' 
				data-type='webSite'
				data-tablehaspart='webSite' 
				data-apihost='".App::getApiHost()."'
				data-columnsTable='{\"edit\":\"Edit\", \"idwebSite\": \"id\", \"name\":\"Nome\"}'
				data-usertoken='".CmsFactory::request()->user()->userLogged()->getToken()."'
			></div>
		");
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
    //WebPageView::index($value);
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
