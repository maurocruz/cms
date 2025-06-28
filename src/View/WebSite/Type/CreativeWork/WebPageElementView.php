<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class WebPageElementView extends CreativeWorkView implements TypeViewInterface
{
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
	private ?string $idwebPageElement = null;
	/**
	 * @var string|null
	 */
	private ?string $webPageElementName = null;

	/**
	 *
	 */
	public function __destruct()
	{
		self::navBarWebPageElement();
	}

	/**
	 * @return void
	 */
  private function navBarWebPageElement(): void
  {
	  CmsFactory::view()->addHeader(
		  CmsFactory::view()->fragment()->navbar()
			  ->type('WebPageElement')
			  ->title('WebPage Element')
			  ->level(7)
			  ->newTab("/admin/webPageElement?idHasPart=$this->idHasPart&typeHasPart=$this->typeHasPart",CmsFactory::view()->fragment()->icon()->home())
			  ->newTab("/admin/webPageElement/new?idHasPart=$this->idHasPart&typeHasPart=$this->typeHasPart",CmsFactory::view()->fragment()->icon()->plus())
			  ->ready()
	  );
    if ($this->idwebPageElement && $this->webPageElementName) {
			CmsFactory::view()->addHeader(
        CmsFactory::view()->fragment()->navbar()->type('WebPageElement')
	        ->title($this->webPageElementName)
	        ->level(7)
	        ->newTab('/admin/webPageElement/edit/'.$this->idwebPageElement, CmsFactory::view()->fragment()->icon()->home())
	        ->newTab("/admin/webPageElement?idHasPart=$this->idcreativeWork&typeHasPart=webPageElement",_('WebPage elements'))
	        ->ready()
			);
    }
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
			$isPartOf = $value['isPartOf'];
			// Has part
			$tbHasPart = CmsFactory::toolBox()::typeBuilder($value);
			$this->idHasPart = $tbHasPart->getPropertyValue('idcreativeWork');
			$this->typeHasPart = $tbHasPart->getType();
			// IS PART OF
			$tbIsPartOf = CmsFactory::toolBox()::typeBuilder($isPartOf);
			if ($this->typeHasPart == 'WebPage') {
				WebSiteView::navbarWebSite($tbIsPartOf->getValue('name'), $tbIsPartOf->getId());
				WebPageView::navbarWebPage($tbIsPartOf->getId(), $tbHasPart->getId(), $tbHasPart->getValue('name'), $this->idHasPart);
			}
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPageElement')->setDataset('params',"isPartOf=$this->idHasPart")->setColumnsTable(['url'=>'Url'])->ready());
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
			$isPartOf = is_string($value['isPartOf']) ? $value['isPartOf'] : CmsFactory::toolBox()::typeBuilder($value['isPartOf'])->getId();
			if ($this->typeHasPart == 'WebPage') {
				$webSite = $value['isPartOf'];
				$tbWebSite = CmsFactory::toolBox()::typeBuilder($webSite);
				WebSiteView::navbarWebSite($tbWebSite->getValue('name'), $tbWebSite->getId());
				WebPageView::navbarWebPage($isPartOf, $tbHasPast->getId(), $tbHasPast->getValue('name'), $tbHasPast->getPropertyValue('idcreativeWork'));
			}
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
			$tbWebPageElement = CmsFactory::toolBox()::typeBuilder($value);
			$this->idwebPageElement = $tbWebPageElement->getId();
			$this->webPageElementName = $tbWebPageElement->getValue('name');
			$this->idthing = $tbWebPageElement->getIdthing();
			$this->idcreativeWork = $tbWebPageElement->getPropertyValue('idcreativeWork');
			$tbIsPartOf = CmsFactory::toolBox()::typeBuilder($value['isPartOf']);
			$this->idHasPart = $tbIsPartOf->getPropertyValue('idcreativeWork');
			$this->typeHasPart = $tbIsPartOf->getType();
			if ($tbIsPartOf->getType() == "WebPage") {
				WebPageView::navbarWebPage($tbIsPartOf->getValue('isPartOf'), $tbIsPartOf->getId(), $tbIsPartOf->getValue('name'), $this->idHasPart);
			}
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::editForms($value), _("Web page element")));
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->noContent(_("No items found!"))
			);
		}

  }

  /**
   * @param array $value
   * @return array
   * @throws Exception
   */
  public function editForms(array $value): array
  {
    // FORM CONTENT
    $content[] = self::formWebPageElement("edit", $value);
    // Properties
    $content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPageElement", $this->idthing, $value['identifier']));
    // IMAGES
	  $content[] = CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready();
		// RETURN
    return $content;
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
    // NAME
    $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
    // POSITION
    $form->fieldsetWithInput('position', $position ? (string) $position : null, _('Position'));
		// headline
	  $form->fieldsetWithInput('headline', $headline, _('Title'));
    // TEXT
    $form->fieldsetWithTextarea('text', htmlentities($text), _('Text'), null, ["id"=>"textareaWebPageElement$id"]);
    $form->setEditor("textareaWebPageElement$id", "editor$case$id");
    // SUBMIT BUTTONS
    $form->submitButtonSend();
    if ($case=='edit') $form->submitButtonDelete("/admin/webPageElement/erase");
    // READY
    return $form->ready();
  }
}
