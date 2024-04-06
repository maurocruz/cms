<?php
declare(strict_types=1);
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
  protected function navbarIndex()
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::view()->fragment()->navbar()
		    ->setTitle(_("Organization"))
		    ->setTabs([
	      "/admin/organization"=> CmsFactory::view()->fragment()->icon()->home(),
	      "/admin/organization/new" => CmsFactory::view()->fragment()->icon()->plus()
	    ])->level(2)->search('organization')->ready()
		);
  }
  /**
   *
   */
  protected function navbarNew()
  {
    $this->navbarIndex();
    CmsFactory::view()->fragment()->navbar()->title(_("Add new"))->content([])->level(3)->ready();
  }
  /**
   *
   */
  protected function navbarEdit()
  {
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($this->name)
				->level(3)
				->newTab("/admin/organization/edit?id=$this->idorganization", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/organization?id=$this->idorganization&action=service", _("Services"))
				->newTab("/admin/organization/product?id=$this->idorganization", _("Products"))
				->newTab("/admin/organization/order?id=$this->idorganization", _("Orders"))
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
