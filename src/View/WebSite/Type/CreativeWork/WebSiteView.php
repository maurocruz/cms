<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class WebSiteView extends CreativeWorkView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	protected ?string $idwebSite = null;
	/**
	 * @var string|null
	 */
	protected ?string $webSiteName = null;

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function __destruct()
	{
		self::navbarWebSite($this->webSiteName, $this->idwebSite);
	}

	/**
	 * @param string|null $webSiteName
	 * @param string|null $idwebSite
	 * @return void
	 */
	public static function navbarWebSite(string $webSiteName = null, string $idwebSite = null): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('webSite')
				->title("WebSite")
				->level(3)
				->newTab('/admin/webSite', CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/webSite/new', CmsFactory::view()->fragment()->icon()->plus())
				->search()
				->ready()
		);
		if ($webSiteName) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title(_($webSiteName))
					->level(4)
					->newTab("/admin/webSite/edit/$idwebSite", CmsFactory::view()->fragment()->icon()->home())
					->newTab("/admin/webPage?idwebSite=$idwebSite", _("Pages"))
					->ready()
			);
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
  public function index(?array $data, array $queryParams = null): void
  {
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webSite')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function new(?array $data, array $queryParams = null): void
  {
    // FORM
    CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formWebSite(), _('Add new'))
    );
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function edit(?array $data, array $queryParams = null): void
  {
    $value = $data[0] ?? null;
	  if ($value) {
			$typeBuilder = ToolBox::typeBuilder($value);
			$this->idwebSite = $typeBuilder->getId();
			$this->webSiteName = $value['name'];
			$this->idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
			// form
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formWebSite($value), $value['name'])
			);
			// list of webPages
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setColumnsTable(['url'=>'Url'])->setIdIsPartOf($this->idcreativeWork)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('Nothing found!'));
		}
  }

	/**
	 * @param array|null $value
	 * @return array
	 */
	protected static function formWebSite(array $value = null): array
	{
		//vars
		$id = $value['idwebSite'] ?? null;
		$copyrightHolder = $value['copyrightHolder'] ?? null;
		$author = $value['author'] ?? null;
		$case = $id ? 'edit' : 'new';

		// form
		$form = CmsFactory::view()->fragment()->form("form-webSite",['class'=>'form-basic form-webSite']);
		$form->action("/admin/webSite/$case")->method('post');
		// hidden
		if ($id) $form->input('idwebSite',(string) $id,'hidden');
		// thing
		$form = ThingView::formContent($form, $value);
		// copyrightHolder
		$form->chooseType(_( 'Copyright holder' ), 'copyrightHolder', array("Organization","Person"),$copyrightHolder);
		// author
		$form->relationshipOneToOne('person',_('Author'),'author',(int) $author);
		// submit
		$form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
		if ($id) {
			$form->submitButtonDelete('/admin/webSite/erase',['class'=>'form-submit-button form-submit-button-delete']);
		}
		// ready
		return $form->ready();
	}
}
