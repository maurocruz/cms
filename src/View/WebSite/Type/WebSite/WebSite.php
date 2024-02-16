<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class WebSite extends WebSiteAbstract implements TypeInterface
{
	/**
	 * @param array|null $value
	 */
  public function index(?array $value) {
    $this->navbarWebSite();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webSite')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   * @param array|null $value
   */
  public function new(?array $value) {
    // NAVBAR
    $this->navbarWebSite();
    // FORM
    CmsFactory::view()->addMain(self::newView());
  }

  /**
   * @param ?array $value
   */
  public function edit(?array $value) {
    $value = $value[0] ?? null;
		if ($value) {
			$this->setIdwebSite($value['idwebSite']);
			// navbar
			parent::navbarWebSite($value['name']);
			// form
			CmsFactory::view()->addMain(parent::editView($value));
		} else {
			parent::navbarWebSite();
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('Nothing found!'));
		}
  }

  /*public function webPage(?array $value)
  {
		if(!$value) {
			parent::navbarWebSite();
			return CmsFactory::view()->fragment()->noContent();
		}
		$webPage = CmsFactory::view()->webSite()->type('webPage');
    // ITEM
	  $this->idwebSite = $value['idwebSite'];
	  if ($value['@type'] == "WebPage") {
		  parent::navbarWebSite($value['isPartOf']['name']);
      WebPage::edit($value);
    } else {
		  // navbar
      parent::navbarWebSite($value['name']);
      if (isset($value['hasPart'])) {
				return $webPage->setMethodName('index')->setData($value)->ready();
        // LIST ALL
      } elseif(isset($value['sitemaps'])) {
        WebPage::sitemap($value);
      } else {
        // NEW WEB PAGE
	      return $webPage->setMethodName('new')->setData($value)->ready();
      }
    }
  }*/

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
}
