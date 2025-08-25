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
	protected ?string $idHasPart = null;
	/**
	 * @var string|null
	 */
	protected ?string $typeHasPart = null;
	/**
	 * @var string|null
	 */
	private ?string $webPageElementName = null;

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
		  ->newTab("/admin/webPageElement?idHasPart=$this->idHasPart&typeHasPart=$this->typeHasPart",CmsFactory::view()->fragment()->icon()->home())
		  ->newTab("/admin/webPageElement/new?idHasPart=$this->idHasPart&typeHasPart=$this->typeHasPart",CmsFactory::view()->fragment()->icon()->plus())
		  ->ready();
    if ($this->idwebPageElement && $this->webPageElementName) {
			$navbarItem = CmsFactory::view()->fragment()->navbar()
				->type('WebPageElement')
	      ->title($this->webPageElementName)
        ->level(4)
        ->newTab('/admin/webPageElement/edit/'.$this->idwebPageElement, CmsFactory::view()->fragment()->icon()->home())
        ->newTab("/admin/webPageElement?idHasPart=$this->idcreativeWork&typeHasPart=webPageElement",_('WebPage elements'))
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
			$this->idwebPage = $tbHasPart->getId();
			$this->typeHasPart = $tbHasPart->getType();
			$this->idthing = $tbHasPart->getIdthing();
			$this->webPageName = $tbHasPart->getValue('name');
			// webSite
			$tbIsPartOf = CmsFactory::toolBox()::typeBuilder($value['isPartOf']);
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
			$tbHasPast =  CmsFactory::toolBox()::typeBuilder($value);
			$this->idHasPart = $tbHasPast->getPropertyValue('idcreativeWork');
			$this->typeHasPart = $tbHasPast->getType();
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
			// web page
			$webPage = $value['isPartOf'];
			$tbIsPartOf = CmsFactory::toolBox()::typeBuilder($webPage);
			$this->idwebPage = $tbIsPartOf->getId();
			$this->webPageName = $tbIsPartOf->getValue('name');
			$this->idHasPart = $tbIsPartOf->getPropertyValue('idcreativeWork');
			$this->typeHasPart = $tbIsPartOf->getType();
			// webSite
			$webSite = $webPage['isPartOf'];
			$tbWebSite = CmsFactory::toolBox()::typeBuilder($webSite);
			$this->idwebSite = $tbWebSite->getId();
			$this->webSiteName = $tbWebSite->getValue('name');
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPageElement('edit',$value), _("Web page element")));
			// Properties
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPageElement", $this->idthing, $value['identifier'])));
			// HAS PART REACT
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPageElement')->setIdHasPart($this->idthing)->ready());
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
		$headline = $value['headline'] ?? null;
		$position = $value['position'] ?? null;
		$text = $value['text'] ?? '';
    $form = CmsFactory::view()->fragment()->form("form-webPageElement",['class'=>'form-basic form-webPageElement']);
    $form->action("/admin/webPageElement/$case")->method('post');
		$form->setIdform($id ? "webPageElement$id" : "webPageElement$case");
		$form->addMandatories('name');
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
