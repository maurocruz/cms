<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPoint;
use Plinct\Tool\ArrayTool;

class LocalBusinessView
{
  /**
   *
   */
  public function navbarLocalBussines(string $title = null)
  {
    CmsFactory::webSite()->navbar(_("Locals business"), [
        "/admin/localBusiness" => CmsFactory::response()->fragment()->icon()->home(),
        "/admin/localBusiness/new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 2, ['table'=>'localBusiness']);

    if ($title) CmsFactory::webSite()->navbar($title, [], 3);
  }

  /**
   * @param $data
   */
  public function index($data)
  {
    $this->navbarLocalBussines();

    $listTable = CmsFactory::response()->fragment()->listTable();
    $listTable->caption(sprintf(_("List of %s"), "Local business"));
    $listTable->labels(_('Name'), _("Additional type"), _("Date modified"));
    $listTable->rows($data['itemListElement'],['name','additionalType','dateModified']);
    $listTable->setEditButton("/admin/localBusiness?id=");
    CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   *
   */
  public function new()
  {
    $this->navbarLocalBussines();
    CmsFactory::webSite()->addMain(
			CmsFactory::response()->fragment()->box()->simpleBox(self::formLocalBussiness(), _("Localbusiness"))
    );
  }

  /**
   * @throws Exception
   */
  public function edit($data)
	{
    $value = $data[0];
    $id = $value['idlocalBusiness'];
		
    $this->navbarLocalBussines($value['name']);

    // LOCAL BUSINESS
    $content[] = CmsFactory::response()->fragment()->box()->simpleBox(self::formLocalBussiness("edit", $value), _("LocalBusiness"));
    // LOCATION
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Place"), CmsFactory::response()->fragment()->form()->relationshipOneToOne("localBusiness", $id, "location", "place", $value['location']));
    // CONTACT POINT
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm("localBusiness", $id, $value['contactPoint']));
    // ORGANIZATION
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Organization"), CmsFactory::response()->fragment()->form()->relationshipOneToOne("localBusiness", $id, "organization", "organization", $value['organization']));
    // MEMBER
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Persons"), CmsFactory::response()->fragment()->form()->relationshipOneToMany("localBusiness", $id, "person", $value['member']));
    // IMAGE
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("localBusiness", $id, $value['image']));

    CmsFactory::webSite()->addMain($content);
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  private static function formLocalBussiness(string $case = "new", $value = null): array
  {
    $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;

    $form = CmsFactory::response()->fragment()->form(["id"=>"form-localBusiness", "class" => "formPadrao form-localBusiness"]);
    $form->action("/admin/localBusiness/$case")->method('post');
    // hiddens
    if ($case == "edit") $form->input("idlocalBusiness", $id, "hidden");
    // name
    $form->fieldsetWithInput("name", $value['name'] ?? null, _("Name"));
    // description
    $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
    // disambiguatingDescription
    $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
    // hasOfferCatalog
    $form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Offer catalog"));
    // url
    $form->fieldsetWithInput("url", $value['url'] ?? null, "Url");
    // dateCreated
    if ($case == "edit") $form->fieldsetWithInput("dateCreated", $value['dateCreated'] ?? null, _("Date created"), "datetime", null, [ "disabled" ]);
    // dateModified
    if ($case == "edit") $form->fieldsetWithInput("dateModified", $value['dateModified'] ?? null, _("Date modified"), "datetime", null, [ "disabled" ]);
    // submit buttons
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/localBusiness/erase");
    // ready
    return $form->ready();
  }
}
