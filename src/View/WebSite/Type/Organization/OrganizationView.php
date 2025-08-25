<?php
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPointView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class OrganizationView extends ThingView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	private ?string $idorganization = null;

	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'organization')
	{
		parent::__construct($type);
	}

	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('Organization')
				->setTitle(_("Organization"))
				->newTab("/admin/organization", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/organization/new", CmsFactory::view()->fragment()->icon()->plus())
				->newTab("/admin/localBusiness",_("Local Business"))
				->ready()
		);
		if($this->name && $this->idorganization && $this->idthing) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($this->name)
				->level(3)
				->newTab("/admin/organization/edit?idorganization=$this->idorganization", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/localBusiness?organization=$this->idorganization",_("Local Business"))
				->newTab("/admin/service?provider=$this->idthing", _("Services"))
				->newTab("/admin/product?manufacturer=$this->idthing", _("Products"))
				->newTab("/admin/order?seller=$this->idthing", _("Orders"))
				->newTab("/admin/role?refererType=Organization&refererName=$this->name&refererId=$this->idorganization&refererIdthing=$this->idthing", _("Members"))
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('organization')->ready()
		);
	}
  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @param
   */
  public function new(?array $data, array $queryParams = null): void
  {
    CmsFactory::View()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox( self::formOrganization(), _("Add organization"))
    );
  }
  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		// NAVBAR
		if (isset($data['status']) && $data['status']=='fail') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
		} elseif (!empty($data)) {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			$this->idorganization =  $typeBuilder->getId();
			$this->idthing = $typeBuilder->getPropertyValue('idthing');
			$this->name = $value['name'];
			// ORGANIZATION
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Organization"), self::formOrganization('edit', $value), true)
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), ContactPointView::getForm('organization', $this->idthing, $value['contactPoint'] ?? null))
			);
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Organization is not exists!")));
		}
  }

	/**
	 * FORM EDIT AND NEW
	 * @param string $case
	 * @param null $value
	 * @return array
	 */
	private function formOrganization(string $case = 'new', $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-organization", ["class" => "form-basic form-organization"]);
		$form->action("/admin/organization/$case")->method("post");
		// HIDDEN
		if ($case == "edit") $form->input("idorganization", (string) $this->idorganization, 'hidden');
		// THING
		$form = parent::formThingContent($form, $value);
		// legal name
		$form->fieldsetWithInput("legalName", $value['legalName'] ?? null, _("Legal Name"));
		// tax id
		$form->fieldsetWithInput("taxId", $value['taxId'] ?? null, _("Tax Id"));
		// has offered a catalog
		$form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Has offer catalog"));
		//submit
		$form->submitButtonSend();
		if ($case == "edit") $form->submitButtonDelete('/admin/organization/delete');
		// READY
		return $form->ready();
	}
}
