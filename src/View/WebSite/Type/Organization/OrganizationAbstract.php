<?php
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;

abstract class OrganizationAbstract
{
  /**
   * @var array
   */
  protected array $content = [];

	protected string $idthing;
  /**
   * @var int
   */
  protected int $idorganization;
  /**
   * @var string
   */
  protected string $name;

  /**
   * INDEX NAVBAR
   */
  public static function navbarIndex(): void
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::view()->fragment()->navbar()
		    ->type('Organization')
		    ->setTitle(_("Organization"))
		    ->newTab("/admin/organization", CmsFactory::view()->fragment()->icon()->home())
		    ->newTab("/admin/organization/new", CmsFactory::view()->fragment()->icon()->plus())
		    ->ready()
		);
  }

  /**
   *
   */
  protected function navbarNew(): void
  {
    $this->navbarIndex();
    CmsFactory::view()->fragment()->navbar()->title(_("Add new"))->content([])->level(3)->ready();
  }
  /**
   *
   */
  public static function navbarEdit(string $name, int $idorganization, string $idthing): void
  {
	  self::navbarIndex();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($name)
				->level(3)
				->newTab("/admin/organization/edit?idorganization=$idorganization", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/service?provider=$idthing", _("Services"))
				->newTab("/admin/product?manufacturer=$idthing", _("Products"))
				->newTab("/admin/order?seller=$idthing", _("Orders"))
				->newTab("/admin/role?refererType=Organization&refererName=$name&refererId=$idorganization&refererIdthing=$idthing", _("Members"))
				->ready()
		);
  }

  /**
   * FORM EDIT AND NEW
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formOrganization(string $case = 'new', $value = null): array
  {
    $form = CmsFactory::view()->fragment()->form("form-organization", ["class" => "form-basic form-organization"]);
    $form->action("/admin/organization/$case")->method("post");
		// HIDDEN
    if ($case == "edit") $form->input("idorganization", (string) $this->idorganization, 'hidden');
		// THING
		$form = ThingView::formThing($form, $value);
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
