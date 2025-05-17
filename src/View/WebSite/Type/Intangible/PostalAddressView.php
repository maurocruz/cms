<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;

class PostalAddressView
{
  /**
   * @param string $typeHasPart
   * @param string $idHasPart
   * @param string $case
   * @param null $value
   * @return array
   */
  public static function formPostalAddress(string $typeHasPart, string $idHasPart, string $case = 'new', $value = null): array
  {
		$tbAddress = CmsFactory::toolBox()::typeBuilder($value);
		$idpostalAddress = $tbAddress->getId();
    $form = CmsFactory::view()->fragment()->form("form-postalAddress",["class" => "form-basic form-postalAddress"]);
		$form->action("/admin/postalAddress/$case")->method("post");
    // hiddens
    $form->input('typeHasPart', $typeHasPart, "hidden");
    $form->input('idHasPart', $idHasPart, "hidden");
    if ($case == "edit") $form->input("idpostalAddress", $idpostalAddress, "hidden");
    // streetAddress
    $form->fieldsetWithInput("streetAddress", $value['streetAddress'] ?? null, _("Street address"));
    // addressLocality
    $form->fieldsetWithInput("addressLocality", $value['addressLocality'] ?? null, _("Address locality"));
    // addressRegion
    $form->fieldsetWithInput("addressRegion", $value['addressRegion'] ?? null, _("Address region"));
    // addressCountry
    $form->fieldsetWithInput("addressCountry", $value['addressCountry'] ?? null, _("Address country"));
    // postalCode
    $form->fieldsetWithInput("postalCode", $value['postalCode'] ?? null, _("Postal code"));
    // submits
    $form->submitButtonSend();
    if ($case =="edit") $form->submitButtonDelete("/admin/postalAddress/erase");
    // ready
    return $form->ready();
  }
}
