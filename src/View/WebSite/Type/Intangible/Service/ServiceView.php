<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Service;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\OrganizationView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Tool\ToolBox;

class ServiceView extends OrganizationView
{
	private ?string $nameService = null;

	public function __construct(string $type = 'service', string $sitemapFilename = 'sitemap-service.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();

		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_("Services"))
				->type('service')
				->level(4)
				->newTab("/admin/service?provider=$this->provider", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/service/new?provider=$this->provider", CmsFactory::view()->fragment()->icon()->plus())
				->ready()
		);
		if ($this->nameService) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
			->title($this->nameService)
			->level(5)
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
		$tb = ToolBox::typeBuilder($data);
		$this->provider = $tb->getPropertyValue('idthing');
		$this->idthing = $tb->getPropertyValue('idthing');
		$this->name = $tb->getValue('name');
		if ($tb->getType() == 'Organization') {
			$this->idorganization = $tb->getId();
		}
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('service')->setIdHasPart($this->provider)->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		if (!empty($data)) {
			$tbProvider = ToolBox::typeBuilder($data);
			$this->provider = $tbProvider->getPropertyValue('idthing');
		}
		// FORM
		CmsFactory::view()->addMain(self::serviceForm());
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (empty($data)) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message());
		} else {
			$value = $data[0];
			$TBService = ToolBox::typeBuilder($value);
			$this->idthing = $TBService->getIdthing();
			$this->nameService = $value['name'];
			$provider = $value['provider'];
			$TBProvider = ToolBox::typeBuilder($provider);
			$this->provider = $TBProvider->getIdthing();
			if($provider['@type'] == 'Organization') {
				$this->idorganization = $TBProvider->getId();
				$this->name = $provider['name'];
			}
			// EDIT SERVICE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::serviceForm('edit', $value),_('Service'), $this->idthing, $this->nameService));
			// OFFERS
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('offer')->setDataset('itemOffered',$this->idthing)->setDataset('offeredBy',$this->provider)->ready());
			// REVIEW
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('review')->setIdHasPart($this->idthing)->ready());
		}
	}

	/**
	 * @param string $case
	 * @param null $value
	 * @return array
	 */
	private function serviceForm(string $case = "new", $value = null): array
	{
		if ($value) {
			$TbService = ToolBox::typeBuilder($value);
			$idservice = $TbService->getId();
		}
		$form = CmsFactory::view()->fragment()->form("form-service", ['class'=>'form-basic form-service']);
		$form->action("/admin/service/$case")->method("post");
		$form->setIdform(isset($idservice) ? "form-service-$idservice" : "form-service-new");
		$form->addMandatories('provider');

		// HIDDENS
		if ($case == 'edit') $form->input('idservice', $idservice,'hidden');
		// THING
		$form = ThingView::formThingContent($form, $value);
		// PROVIDER
		$form->chooseType(_('Provider'),'provider','Organization,Person', $this->provider);
		// SERVICE OUTPUT
		$form->chooseType(_('Service output'),'serviceOutput','CreativeWork,LocalBusiness',$value['serviceOutput'] ?? null);
		// CATEGORY
		$form->fieldsetWithInput('category',$value['category'] ?? null, _('Category'));
		// SERVICE TYPE
		$form->fieldsetWithInput('serviceType',$value['serviceType'] ?? null, _('Service Type'));
		// TERMS OF SERVICE
		$form->fieldsetWithTextarea('termsOfService', $value['termsOfService'] ?? null, _("Terms of service"));
		// SUBMIT BUTTONS
		$form->submitButtonSend();
		if ($case == "edit") $form->submitButtonDelete("/admin/service/erase");
		// RENDER
		return $form->ready();
	}
}
