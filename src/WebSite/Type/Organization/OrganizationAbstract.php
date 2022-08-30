<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Organization;

use Exception;
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
    CmsFactory::webSite()->navbar(_("Organization"), [
      "/admin/organization"=> CmsFactory::response()->fragment()->icon()->home(),
      "/admin/organization/new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 2, ['table'=>'organization']);
  }

  /**
   *
   */
  protected function navbarNew()
  {
    $this->navbarIndex();
    CmsFactory::webSite()->navbar(_("Add new"), [], 3);
  }

  /**
   *
   */
  protected function navbarEdit()
  {
    CmsFactory::webSite()->navbar($this->name, [
      "/admin/organization/edit?id=$this->id" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/organization?id=$this->id&action=service" => _("Services"),
      "/admin/organization/product?id=$this->id" => _("Products"),
      "/admin/organization/order?id=$this->id" => _("Orders")
    ], 3);
  }

  /**
   * FORM EDIT AND NEW
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formOrganization(string $case = 'new', $value = null): array
  {
    $form = CmsFactory::response()->fragment()->form(["name" => "form-organization", "id" => "form-organization", "class" => "formPadrao form-organization"]);
    $form->action("/admin/organization/$case")->method("post");
    $form->content([ "tag" => "h3", "content" => $value['name'] ?? null ]);
    if ($case == "edit") $form->input("idorganization", $value['idorganization'], 'hidden');
    // name
    $form->fieldsetWithInput("name", $value['name'] ?? null, _("Name"));
    // ADDITIONAL TYPE
    $form->fieldset(CmsFactory::response()->fragment()->form()->selectAdditionalType('organization',$value['additionalType'] ?? null), _("Additional type"));
    // legal name
    $form->fieldsetWithInput("legalName", $value['legalName'] ?? null, _("Legal Name"));
    // tax id
    $form->fieldsetWithInput("taxId", $value['taxId'] ?? null, _("Tax Id"));
    // description
    $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
    // disambiguatingDescription
    $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
    // has offer catalog
    $form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Has offer catalog"));
    // url
    $form->fieldsetWithInput("url", $value['url'] ?? null, "Url");
    //submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete('/admin/organization/delete');
    // READY
    return $form->ready();
  }

  /**
   * @throws Exception
   */
  /*protected function itemView(string $itemType, $data): array
  {
      $itemProperty = null;
      $itemResponse = null;
      $itemView = null;
      $value = $data[0];
      $action = filter_input(INPUT_GET, 'action');
      $itemText = lcfirst($itemType)."s";

      // SET ITEM PROPERTY
      if ($itemType == "Product") {
          $itemProperty = "manufacturer";
          $itemView = new ProductView();
      }
      if ($itemType == "Service") {
          $itemProperty = "provider";
          $itemView = new ServiceView();
      }
      if ($itemType == "Order") {
          $itemProperty = "seller";
          $itemView = new OrderView();
      }
      // NO CONTENT
      if (!$itemProperty || !is_object($itemView)) {
          $this->addContent('main', CmsFactory::response()->fragment()->noContent("No item detected"));
          return $this->content;
      }
      // NEW
      if ($action == "new") {
          $itemResponse = $itemView->newWithPartOf([ $itemProperty => $value ]);
      }
      // NO CONTENT
      elseif ($value['@type'] == "Organization" && empty($value[$itemText])) {
          $this->addContent('main', [ "tag" => "p", "content" => _("No $itemText added") ]);
      }
      // EDIT
      elseif ($value['@type'] == $itemType) {
          $itemResponse = $itemView->editWithPartOf($value);
      }
      // INDEX
      else {
          $itemResponse = $itemView->indexWithPartOf($value);
      }
      $this->content['main'] = $itemResponse['main'] ?? null;
      // RESPONSE
      return $this->content;
  }*/
}
