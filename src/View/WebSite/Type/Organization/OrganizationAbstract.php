<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Plinct\Cms\CmsFactory;
use Plinct\Tool\ArrayTool;

abstract class OrganizationAbstract
{
  /**
   * @var array
   */
  protected array $content = [];
  /**
   * @var string
   */
  protected string $id;
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
    $this->id = ArrayTool::searchByValue($organization['identifier'], "id",'value');
    $this->name = $organization['name'];
    return $value;
  }
  /**
   * @param string $target
   * @param $content
   */
  protected function addContent(string $target, $content) {
    $this->content[$target][] = $content;
  }
  /**
   * INDEX NAVBAR
   */
  protected function navbarIndex()
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::view()->fragment()->navbar()->setTitle(_("Organization"))->setTabs([
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
    CmsFactory::view()->fragment()->navbar($this->name, [
      "/admin/organization/edit?id=$this->id" => CmsFactory::view()->fragment()->icon()->home(),
      "/admin/organization?id=$this->id&action=service" => _("Services"),
      "/admin/organization/product?id=$this->id" => _("Products"),
      "/admin/organization/order?id=$this->id" => _("Orders")
    ], 3);
  }

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
  /**
   * FORM EDIT AND NEW
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formOrganization(string $case = 'new', $value = null): array
  {
    $form = CmsFactory::view()->fragment()->form(["name" => "form-organization", "id" => "form-organization", "class" => "formPadrao form-organization"]);
    $form->action("/admin/organization/$case")->method("post");
    $form->content([ "tag" => "h3", "content" => $value['name'] ?? null ]);
    if ($case == "edit") $form->input("idorganization", (string) $value['idorganization'], 'hidden');
    // name
    $form->fieldsetWithInput("name", $value['name'] ?? null, _("Name"));
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
