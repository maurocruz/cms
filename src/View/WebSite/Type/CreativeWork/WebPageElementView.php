<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class WebPageElementView extends WebPageView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	private ?string $idwebPageElement = null;
	/**
	 * @var string|null
	 */
	private ?string $webPageElementName = null;
	protected ?string $webPageElementThing = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'webPageElement', string $sitemapFilename = 'sitemap-webPageElement.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();
		$navbar = CmsFactory::view()->fragment()->navbar()
		  ->type('WebPageElement')
		  ->title('WebPage Element')
		  ->level(3)
			->newTab("/admin/webPageElement?webPage=$this->webPageThing", CmsFactory::view()->fragment()->icon()->home())
		  ->newTab("/admin/webPageElement/new?idHasPart=$this->idHasPart&typeHasPart=$this->typeHasPart",CmsFactory::view()->fragment()->icon()->plus())
		  ->ready();
    if ($this->idwebPageElement && $this->webPageElementName) {
			$navbarItem = CmsFactory::view()->fragment()->navbar()
				->type('WebPageElement')
	      ->title($this->webPageElementName)
				->newTab('/admin/webPageElement/edit/'.$this->idwebPageElement, CmsFactory::view()->fragment()->icon()->home())
        ->level(4)
        ->ready();
    }
		$navbarRow = CmsFactory::view()->fragment()->navbarRow();
		$navbarRow->setItems($navbar,$navbarItem ?? null)->setLevel(3);
		CmsFactory::view()->addHeader($navbarRow->render());
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @return void
   */
  public function index(?array $data, array $queryParams = null): void
  {
		if (isset($data[0])) {
			$value = $data[0];
			// web page
			$tbHasPart = CmsFactory::toolBox()::typeBuilder($value);
			$isPartOf = $tbHasPart->getPropertyValue('idcreativeWork');
			$this->webPageThing = $tbHasPart->getIdthing();
			$this->idwebPage = $tbHasPart->getId();
			$this->typeHasPart = $tbHasPart->getType();
			$this->webPageName = $tbHasPart->getValue('name');
			// webSite
			$tbIsPartOf = CmsFactory::toolBox()::typeBuilder($value['isPartOf']);
			$this->webSiteThing = $tbIsPartOf->getIdthing();
			$this->idwebSite = $tbIsPartOf->getId();
			$this->webSiteName = $tbIsPartOf->getValue('name');
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPageElement')->setIdIsPartOf($isPartOf)->setColumnsTable(['position'=>_('Position')])->setOrderBy('position')->setOrdering('asc')->ready());
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->noContent(_("No items found!"))
			);
		}
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
  public function new(?array $data, array $queryParams = null): void
  {
		if (isset($data[0])) {
			$value = $data[0];
			// WEB PAGE
			$tbHasPast =  CmsFactory::toolBox()::typeBuilder($value);
			$this->idHasPart = $tbHasPast->getPropertyValue('idcreativeWork');
			$this->typeHasPart = $tbHasPast->getType();
			$this->webPageThing = $tbHasPast->getIdthing();
			$this->idwebPage = $tbHasPast->getId();
			$this->webPageName = $tbHasPast->getValue('name');
			// WEB SITE
			$webSite = $value['isPartOf'][0] ?? null;
			$tbWebSite = CmsFactory::toolBox()::typeBuilder($webSite);
			$this->webSiteThing = $tbWebSite->getIdthing();
			$this->idwebSite = $tbWebSite->getId();
			$this->webSiteName = $tbWebSite->getValue('name');
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPageElement())
			);
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->noContent(_("No has part found!"))
			);
		}
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @throws Exception
	 */
  public function edit(array|null $data, array $queryParams = null): void
  {
		if (isset($data[0])) {
			$value = $data[0];
			// web page element
			$tbWebPageElement = CmsFactory::toolBox()::typeBuilder($value);
			$this->idwebPageElement = $tbWebPageElement->getId();
			$this->webPageElementName = $tbWebPageElement->getValue('name');
			$this->name = $tbWebPageElement->getValue('name');
			$this->idthing = $tbWebPageElement->getIdthing();
			$this->idcreativeWork = $tbWebPageElement->getPropertyValue('idcreativeWork');
			// is part of
			$webPageElementIsPartOf = $value['isPartOf'] ?? null;
			if ($webPageElementIsPartOf) {
				array_walk($webPageElementIsPartOf, function ($item) {
					if ($item['@type'] === 'WebPage') {
						$typeBuilderWebSite = CmsFactory::toolBox()::typeBuilder($item);
						$this->webPageThing = $typeBuilderWebSite->getIdthing();
						$this->idwebPage = $typeBuilderWebSite->getId();
						$this->webPageName = $typeBuilderWebSite->getValue('name');
						$this->idHasPart = $typeBuilderWebSite->getIdthing();
						$this->typeHasPart = $typeBuilderWebSite->getType();
						$webPageIspartOf = $item['isPartOf'] ?? null;
						if ($webPageIspartOf) {
							array_walk($webPageIspartOf, function ($item) {
								if ($item['@type'] === 'WebSite') {
									$typeBuilderWebSite = CmsFactory::toolBox()::typeBuilder($item);
									$this->webSiteThing = $typeBuilderWebSite->getIdthing();
									$this->idwebSite = $typeBuilderWebSite->getId();
									$this->webSiteName = $typeBuilderWebSite->getValue('name');
								}
							});
						}
					}
				});
			}
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPageElement('edit', $value), _('Edit'), $this->idthing, $this->name));
			// Properties
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPageElement", $this->idthing, $value['identifier'])));
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->ready());
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->noContent(_("No items found!"))
			);
		}
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  private function formWebPageElement(string $case = "new", $value = null): array
  {
    $id = $this->idwebPageElement;
    $form = CmsFactory::view()->fragment()->form("form-webPageElement",['class'=>'form-basic form-webPageElement']);
    $form->action("/admin/webPageElement/$case")->method('post');
		$form->setIdform($id ? "webPageElement$id" : "webPageElement$case");
    // HIDDEN
    if ($case == 'edit') $form->input('idwebPageElement', (string)$this->idwebPageElement, 'hidden');
    if($case == 'new') $form->input('isPartOf', $this->idHasPart, 'hidden');
		// THING
	  $form = parent::formCreativeWorkContent($form,$value);
    // SUBMIT BUTTONS
    $form->submitButtonSend();
    if ($case=='edit') $form->submitButtonDelete("/admin/webPageElement/erase");
    // READY
    return $form->ready();
  }
}
