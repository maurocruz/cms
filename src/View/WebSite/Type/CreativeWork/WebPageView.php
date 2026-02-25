<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class WebPageView extends WebSiteView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	protected ?string $idwebPage = null;
	/**
	 * @var string|null
	 */
	protected ?string $webPageName = null;

	protected ?string $webPageThing = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'WebPage', string $sitemapFilename = 'sitemap-webPage.xml')
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
			->type('webPage')
			->level(5)
			->title(_("Pages"))
			->newTab("/admin/webPage?webSite=$this->webSiteThing", CmsFactory::view()->fragment()->icon()->home())
			->newTab("/admin/webPage/new?webSite=$this->webSiteThing", CmsFactory::view()->fragment()->icon()->plus())
			->newTab("/admin/webPage/sitemap?webSite=$this->webSiteThing", CmsFactory::view()->fragment()->icon()->sitemap())
			->search()
			->ready();
		if($this->webPageThing) {
			$navbarItem = CmsFactory::view()->fragment()->navbar()
				->level(6)
				->title($this->webPageName)
				->type('webPage')
				->newTab("/admin/webPage/edit?thing=$this->webPageThing", CmsFactory::view()->fragment()->icon()->home())
				->ready();
		}
		$navbarRow = CmsFactory::view()->fragment()->navbarRow()->setItems($navbar,$navbarItem ?? null)->setLevel(2);
		CmsFactory::view()->addHeader($navbarRow->render());
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
  public function index(?array $data, array $queryParams = null): void
  {
		if (is_array($data)) {
		$tbIsPartOf = CmsFactory::toolBox()->typeBuilder($data);
	  $this->name = $tbIsPartOf->getValue('name');
		$this->idthing = $tbIsPartOf->getIdthing();
		$this->idwebSite = $tbIsPartOf->getId();
		$this->idcreativeWork = $tbIsPartOf->getPropertyValue('idcreativeWork');
		}
		// list
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setIdHasPart($this->idthing ?? 0)->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function new(?array $data, array $queryParams = null): void
  {
    // NAVBAR
	  $tbWebSite = CmsFactory::toolBox()::typeBuilder($data);
		$this->idwebSite = $tbWebSite->getId();
		$this->webPageName = $tbWebSite->getValue('name');
		$this->name = $tbWebSite->getValue('name');
		$this->idIsPartOf = $this->idwebSite;
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage(), _("Add new webpage")));
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 * @throws Exception
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		// webPage
		$typeBuilder = CmsFactory::toolBox()::typeBuilder($data);
		$this->webPageThing = $typeBuilder->getIdthing();
		$this->idwebPage = $typeBuilder->getId();
		$this->webPageName = $typeBuilder->getValue('name');
		$this->name = $typeBuilder->getValue('name');
		$this->idthing = $typeBuilder->getPropertyValue('idthing');
		$this->idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
		// WEB SITE
		$isPartOf = $data['isPartOf'] ?? null;
		if (is_array($isPartOf)) {
			array_walk($isPartOf, function($item) {
				if ($item['@type'] === 'WebSite') {
					$typeBuilderWebSite = CmsFactory::toolBox()::typeBuilder($item);
					$this->webSiteThing = $typeBuilderWebSite->getIdthing();
					$this->idwebSite = $typeBuilderWebSite->getId();
					$this->webSiteName = $typeBuilderWebSite->getValue('name');
					$this->idIsPartOf = $typeBuilderWebSite->getIdthing();
				}
			});
		}
    // FORM EDIT
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit"), $this->idthing, $this->name));
    // PROPERTIES;
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage",(string) $this->idthing, $data['identifier'])));
		// IMAGE
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->setDataset('orderBy','position')->setDataset('ordering','asc')->ready());
		// list of webPageElement
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPageElement')->setIdHasPart($this->idthing)->setProperty('hasPart')->setDataset('orderBy','position')->setDataset('ordering','asc')->ready());
  }

	/**
	 * * * * * FORM * * * *
	 *
	 * @param array|null $value
	 * @return array
	 */
	protected function formWebPage(array $value = null): array
	{
		// FORM
		$form = CmsFactory::view()->fragment()->form("form-webPage",['class'=>'form-basic form-webPage']);
		$form->action("/admin/webPage/".($value ? 'edit' : 'new'))->method('post');
		// hidden
		$form->input('isPartOf', (string) $this->idIsPartOf ,'hidden');
		if ($value) {
			$form->input('thing', (string) $this->idthing,'hidden');
			$form->input('idwebPage', (string) $this->idwebPage,'hidden');
		}
		// CreativeWork
		$form = parent::formCreativeWorkContent($form, $value, ['alternateName','url','isPartOf']);
		// submit
		$form->submitButtonSend();
		if ($value) $form->submitButtonDelete('/admin/webPage/erase');
		// ready
		return $form->ready();
	}
}
