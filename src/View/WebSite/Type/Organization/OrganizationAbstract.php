<?php
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Tool\ArrayTool;

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
   * @param array $value
   * @return array
   */
  protected function setValues(array $value): array
  {
    $organization = $value['@type'] == 'Organization' ? $value : $value['provider'];
    $this->idorganization = ArrayTool::searchByValue($organization['identifier'], "id",'value');
    $this->name = $organization['name'];
    return $value;
  }

  /**
   * INDEX NAVBAR
   */
  public static function navbarIndex(): void
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::view()->fragment()->navbar()
		    ->type('Organization')
		    ->setTitle(_("Organization"))
		    ->newTab("/admin/organization", CmsFactory::view()->fragment()->icon()->home(16,16))
		    ->newTab("/admin/organization/new", CmsFactory::view()->fragment()->icon()->plus(16,16))
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
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($name)
				->level(3)
				->newTab("/admin/organization/edit?idorganization=$idorganization", CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab("/admin/service?provider=$idthing", _("Services"))
				->newTab("/admin/product?manufacturer=$idthing", _("Products"))
				->newTab("/admin/order?seller=$idthing", _("Orders"))
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
    $form = CmsFactory::view()->fragment()->form(["class" => "form-basic form-organization"]);
    $form->action("/admin/organization/$case")->method("post");
		// HIDDEN
    if ($case == "edit") $form->input("idorganization", (string) $this->idorganization, 'hidden');
		// THING
		$form = Thing::formContent($form, $value);
		// legal name
    $form->fieldsetWithInput("legalName", $value['legalName'] ?? null, _("Legal Name"));
    // tax id
    $form->fieldsetWithInput("taxId", $value['taxId'] ?? null, _("Tax Id"));
    // has offer catalog
    $form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Has offer catalog"));
    //submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete('/admin/organization/delete');
    // READY
    return $form->ready();
  }
}
