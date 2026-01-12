<?php
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPointView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class OrganizationView extends ThingView implements TypeViewInterface
{
	protected ?string $provider = null;
	/**
	 * @var string|null
	 */
	protected ?string $idorganization = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'organization', string $sitemapFilename = 'sitemap-organization.xml')
	{
		parent::__construct($type, $sitemapFilename);
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
				->newTab("/admin/localBusiness",_("Local businesses"))
				->ready()
		);
		if($this->name && $this->idorganization && $this->idthing) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($this->name)
				->level(3)
				->newTab("/admin/organization/edit?idorganization=$this->idorganization", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/localBusiness?organization=$this->idorganization",_("Local businesses"))
				->newTab("/admin/service?provider=$this->provider", _("Services"))
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
			$this->idthing = $typeBuilder->getIdthing();
			$this->provider = $this->idthing;
			$this->name = $value['name'];
			$location = $value['location'] ?? null;
			// ORGANIZATION
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formOrganization('edit', $value),_("Organization"),$this->idthing,$this->name)
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), ContactPointView::getForm('organization', $this->idthing, $value['contactPoint'] ?? null))
			);
			// LOCATION
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('place')->setIdHasPart($location)->ready());
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('mediaObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->ready());
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
		$form = $this->formOrganizationContent($form, $value);
		//submit
		$form->submitButtonSend();
		if ($case == "edit") $form->submitButtonDelete('/admin/organization/delete');
		// READY
		return $form->ready();
	}

	public function formOrganizationContent($form, $value): Form
	{
		$legalName = $value['legalName'] ?? null;
		$taxId = $value['taxId'] ?? null;
		$location = $value['location'] ?? null;
		$hasOfferCatalog = $value['hasOfferCatalog'] ?? null;
		// THING
		$form = parent::formThingContent($form, $value);
		//
		if ($this->type !== 'organization') {
			$form->content(CmsFactory::view()->fragment()->box()->expandingBoxWithoutContent(_("Organization"), "form-organization"));
		}
		// legal name
		$form->fieldsetWithInput("legalName", $legalName, _("Legal Name"));
		// tax id
		$form->fieldsetWithInput("taxId", $taxId, _("Tax Id"));
		// location
		$form->relationshipOneToOne('place',_('Place'), 'location', $location ?? null);
		// has offered a catalog
		$form->fieldsetWithInput("hasOfferCatalog", $hasOfferCatalog ?? null, _("Has offer catalog"));
		//
		if ($this->type !== 'organization') {
			$form->content("</div>");
		}
		return $form;
	}
}
