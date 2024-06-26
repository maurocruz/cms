<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class WebPage extends WebPageAbstract implements TypeInterface
{

	/**
	 * @param ?array $value
	 * @return null
	 */
  public function index(?array $value) {
		parent::navbarWebSite($value);
		parent::navbarWebPage();
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   *
   * @param ?array $value
   */
  public function new(?array $value) {
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
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf((int) $this->idthing)->ready());
    // WEB ELEMENTS
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Web page elements"), (new WebPageElement($idcreativeWork))->getForm((string) $this->idwebPage, $data['hasPart'])));
	  return true;
  }
}
