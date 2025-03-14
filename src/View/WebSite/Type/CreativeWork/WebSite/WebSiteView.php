<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class WebSiteView extends WebSiteAbstract implements TypeViewInterface
{
	/**
	 * @param array|null $value
	 */
  public function index(?array $value): void
  {
    $this->navbarWebSite();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webSite')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   * @param array|null $value
   */
  public function new(?array $value): void
  {
    // NAVBAR
    $this->navbarWebSite();
    // FORM
    CmsFactory::view()->addMain(self::newView());
  }

  /**
   * @param ?array $data
   */
  public function edit(?array $data): void
  {
    $value = $data[0] ?? null;
		$typeBuilder = ToolBox::typeBuilder($value);
		$value['idwebSite'] = $typeBuilder->getId();
		$idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
		if ($value) {
			$this->setIdwebSite($value['idwebSite']);
			// navbar
			parent::navbarWebSite($value['name']);
			// form
			CmsFactory::view()->addMain(parent::editView($value));
			// list of webPages
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setColumnsTable(['url'=>'Url'])->setIdIsPartOf($idcreativeWork)->ready());
		} else {
			parent::navbarWebSite();
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('Nothing found!'));
		}
  }

	/**
	 * @return void
	 */
	public function sitemap(): void
	{
		$listType = ['Article','Event'];
		parent::navbarWebSite();
		CmsFactory::view()->addMain("<h1>"._("Sitemaps")."</h1>");
		foreach ($listType as $type) {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_($type), parent::formSitemap($type))
			);
		}
	}
}
