<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork\WebPage;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\WebPageElement;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class WebPageView extends WebPageViewAbstract implements TypeViewInterface
{

	/**
	 * @param ?array $value
	 * @return bool|null
	 */
  public function index(?array $value): ?bool
  {
		$tb = CmsFactory::toolBox()->typeBuilder($value);
		$idIsPartOf = $tb->getId();
		parent::navbarWebSite($value);
		parent::navbarWebPage();
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setIdIsPartOf($idIsPartOf)->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   *
   * @param ?array $value
   */
  public function new(?array $value): void
  {
    // NAVBAR
	  parent::navbarWebSite($value);
    parent::navbarWebPage("Add new webpage");
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage(), _("Add new webpage")));
  }

	/**
	 * @throws Exception
	 */
	public function edit(?array $data): bool {
		$typeBuilder = new TypeBuilder('webPage', $data);
		$webSite = $typeBuilder->getValue("isPartOf");
		$idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
		$typeBuilderWebSite = new TypeBuilder('webSite', $webSite);
	  $this->idwebSite = $typeBuilderWebSite->getId();
	  $this->idwebPage = $typeBuilder->getId();
		$this->idthing = $typeBuilder->getPropertyValue('idthing');

		parent::navbarWebSite($webSite);
    self::navbarWebPage($data['name']);
    // FORM EDIT
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit")));
    // PROPERTIES
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage",(string) $this->idwebPage, $data['identifier'])));
		// IMAGES
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready());
    // WEB ELEMENTS
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Web page elements"), (new WebPageElement($idcreativeWork))->getForm((string) $this->idwebPage, $data['hasPart'] ?? null)));
	  return true;
  }
}
