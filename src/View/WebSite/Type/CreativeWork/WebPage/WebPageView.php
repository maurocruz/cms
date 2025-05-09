<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork\WebPage;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\WebPageElement;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class WebPageView extends WebPageViewAbstract implements TypeViewInterface
{

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return bool|null
	 */
  public function index(?array $data, array $queryParams = null): ?bool
  {
		$tb = CmsFactory::toolBox()->typeBuilder($data);
		$idIsPartOf = $tb->getId();
		parent::navbarWebSite($data);
		parent::navbarWebPage();
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setIdIsPartOf($idIsPartOf)->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   *
   * @param array|null $value
   * @param array|null $queryParams
   */
  public function new(?array $value, array $queryParams = null): void
  {
    // NAVBAR
	  parent::navbarWebSite($value);
    parent::navbarWebPage("Add new webpage");
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage(), _("Add new webpage")));
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return bool
	 * @throws Exception
	 */
	public function edit(?array $data, array $queryParams = null): bool {
		$typeBuilder = CmsFactory::toolBox()::typeBuilder($data);
		$webSite = $typeBuilder->getValue("isPartOf");
		$idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
		$typeBuilderWebSite = CmsFactory::toolBox()::typeBuilder($webSite);
	  $this->idwebSite = $typeBuilderWebSite->getId();
	  $this->idwebPage = $typeBuilder->getId();
		$this->idthing = $typeBuilder->getPropertyValue('idthing');

		parent::navbarWebSite($webSite);
    self::navbarWebPage($data['name']);
    // FORM EDIT
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit")));
    // PROPERTIES
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage",(string) $this->idthing, $data['identifier'])));
		// IMAGES
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready());
    // WEB ELEMENTS
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Web page elements"), (new WebPageElement($idcreativeWork))->getForm((string) $this->idwebPage, $data['hasPart'] ?? null)));
	  return true;
  }
}
