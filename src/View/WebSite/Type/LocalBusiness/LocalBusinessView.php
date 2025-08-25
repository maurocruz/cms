<?php
namespace Plinct\Cms\View\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPointView;
use Plinct\Cms\View\WebSite\Type\Organization\OrganizationView;

class LocalBusinessView extends OrganizationView
{
	/**
	 * @var string
	 */
	private string $idlocalBusiness;

	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'localBusiness')
	{
		parent::__construct($type);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
			->type('localBusiness')
			->title(_('Local business'))
			->newTab('/admin/localBusiness', CmsFactory::view()->fragment()->icon()->home())
			->newTab('/admin/localBusiness/new', CmsFactory::view()->fragment()->icon()->plus())
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
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('localBusiness')->ready()
		);
	}

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function new(?array $data, array $queryParams = null): void
  {
    CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formLocalBussiness(), _("Localbusiness"))
    );
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit($data, array $queryParams = null): void
  {
    $value = $data[0] ?? null;
		if ($value) {
			$tbLocalBusiness = CmsFactory::helpers()->typeBuilder($value);
			$this->idlocalBusiness = $tbLocalBusiness->getId();
			$this->idthing = $tbLocalBusiness->getIdthing();
			$this->name = $tbLocalBusiness->getValue('name');
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formLocalBussiness($value), _("Localbusiness"))
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_('Contact point'),ContactPointView::getForm('localBusiness', $this->idthing, $value['contactPoint'] ?? null))
			);
			// HAS PART
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('localBusiness')->setProperty('hasPart')->setIdHasPart((int) $this->idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent());
		}
  }

	/**
	 * @param array|null $value
	 * @return array
	 */
  private function formLocalBussiness(array $value = null): array
  {
    $form = CmsFactory::view()->fragment()->form('form-localBusiness',['class'=>'formPadrao form-localBusiness']);
    $form->action("/admin/localBusiness/new")->method('post');
		if ($value) {
			$form->input('idlocalBusiness', $this->idlocalBusiness, 'hidden');
		}
		// THING
	  $form = parent::formThingContent($form, $value);
    // submit buttons
    $form->submitButtonSend();
    if ($value) $form->submitButtonDelete("/admin/localBusiness/erase");
    // ready
    return $form->ready();
  }
}
