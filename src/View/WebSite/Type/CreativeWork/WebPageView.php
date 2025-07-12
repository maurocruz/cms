<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;

class WebPageView extends CreativeWorkView
{
	/**
	 * @var string|null
	 */
	private ?string $idIsPartOf = null;
	/**
	 * @var string|null
	 */
	private ?string $idHasPart = null;
	/**
	 * @var string|null
	 */
	protected ?string $idwebPage = null;
	/**
	 * @var string|null
	 */
	protected ?string $webPageName = null;

	/**
	 *
	 */
	public function __construct($type = 'webPage')
	{
		parent::__construct($type);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		if($this->idIsPartOf) {
			self::navbarWebPage($this->idIsPartOf, $this->idwebPage, $this->webPageName, $this->idHasPart);
		}
	}

	/**
	 * @param string $idwebSite
	 * @param string|null $idwebPage
	 * @param string|null $webPageName
	 * @param string|null $idwebPageCreativeWork
	 * @return void
	 */
	public static function navbarWebPage(string $idwebSite, string $idwebPage = null, string $webPageName = null, string $idwebPageCreativeWork = null): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('webPage')
				->level(5)
				->title(_("Pages"))
				->newTab("/admin/webPage?idwebSite=$idwebSite", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/webPage/new?idwebSite=$idwebSite", CmsFactory::view()->fragment()->icon()->plus())
				->newTab("/admin/webPage/sitemap?idwebSite=$idwebSite", CmsFactory::view()->fragment()->icon()->sitemap())
				->search()
				->ready()
		);

		if ($idwebPage && $webPageName && $idwebPageCreativeWork) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->level(6)
				->title($webPageName)
				->type('webPage')
				->newTab("/admin/webPage/edit/$idwebPage", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/webPageElement?idHasPart=$idwebPageCreativeWork&typeHasPart=webPage",_('WebPage elements'))
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
  public function index(?array $data, array $queryParams = null): void
  {
		$tbIsPartOf = CmsFactory::toolBox()->typeBuilder($data);
	  $webSiteName = $tbIsPartOf->getValue('name');
		$this->idIsPartOf = $tbIsPartOf->getId();
		// navbar
		WebSiteView::navbarWebSite($webSiteName, $this->idIsPartOf);
		// list
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setIdIsPartOf($this->idIsPartOf)->setColumnsTable(['url'=>'Url'])->ready());
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
		$this->idIsPartOf = $tbWebSite->getId();
		$webSiteName = $tbWebSite->getValue('name');
	  // navbar
	  WebSiteView::navbarWebSite($webSiteName, $this->idIsPartOf);
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
		$typeBuilder = CmsFactory::toolBox()::typeBuilder($data);
		$webSite = $typeBuilder->getValue("isPartOf");
		$typeBuilderWebSite = CmsFactory::toolBox()::typeBuilder($webSite);
	  $this->idIsPartOf = $typeBuilderWebSite->getId();
		$webSiteName = $typeBuilderWebSite->getValue('name');
	  $this->idwebPage = $typeBuilder->getId();
		$this->webPageName = $typeBuilder->getValue('name');
		$this->idthing = $typeBuilder->getPropertyValue('idthing');
		$this->idHasPart = $typeBuilder->getPropertyValue('idcreativeWork');
		// NAVBAR
		WebSiteView::navbarWebSite($webSiteName, $this->idIsPartOf);
    // FORM EDIT
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit")));
    // PROPERTIES
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage",(string) $this->idthing, $data['identifier'])));
		// IMAGES
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready());
  }

	/**
	 * * * * * FORM * * * *
	 *
	 * @param array|null $value
	 * @return array
	 */
	protected function formWebPage(array $value = null): array
	{
		// VARS
		$headline = $value['headline'] ?? null;
		$alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$text = $value['text'] ?? null;
		$author = $value['author'] ?? null;
		$case = $value ? 'edit' : 'new';
		// FORM
		$form = CmsFactory::view()->fragment()->form("form-webPage",['class'=>'form-basic form-webPage']);
		$form->action("/admin/webPage/$case")->method('post');
		// hidden
		$form->input('isPartOf', (string) $this->idIsPartOf ,'hidden');
		if ($case == "edit") {
			$form->input('thing', (string) $this->idthing,'hidden');
			$form->input('idwebPage', (string) $this->idwebPage,'hidden');
		}
		// THING
		$form = ThingView::formThing($form, $value);
		// HEADLINE
		$form->fieldsetWithInput('headline', $headline, _('Headline'));
		// ALTERNATIVE HEADLINE
		$form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
		// TEXT
		$form->fieldsetWithTextarea('text', htmlentities($text), _("Content"), ['style'=>'width: 100%;'], ['id'=>'contentTextareaWebPage']);
		$form->setEditor('contentTextareaWebPage');
		// AUTHOR
		$form->relationshipOneToOne('person',_('Author'),'author',(int) $author);
		// submit
		$form->submitButtonSend();
		if ($case == "edit") $form->submitButtonDelete('/admin/webPage/erase');
		// ready
		return $form->ready();
	}

	/**
	 * @throws Exception
	 */
	public function sitemap(array $data, array $queryParams = null): void
	{
		$this->idIsPartOf = $queryParams['idwebSite'] ?? null;
		$this->sitemapFilename = 'sitemap-webPage.xml';
		parent::sitemap($data);
	}
}
