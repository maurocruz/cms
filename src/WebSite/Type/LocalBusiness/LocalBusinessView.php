<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPoint;

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
		$apiHost = App::getApiHost();
    $content[] = "<script src='https://plinct.com.br/static/dist/plinct-thing/main.0cc1b1b5e3cba5b9653f.js'></script>";
    $content[] = "<div id='plinctThing' data-type='LocalBusiness' data-id='$id' data-apiHost='$apiHost'></div>";

    //$content[] = CmsFactory::response()->fragment()->box()->simpleBox(self::formLocalBussiness("edit", $value), _("LocalBusiness"));
		// ADDITIONAL TYPE
		//$content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Additional type"), '<div>Development additional type functions</div>');
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
   * @return array
   */
  private static function formLocalBussiness(): array
  {
    $form = CmsFactory::response()->fragment()->form(["id"=>"form-localBusiness", "class" => "formPadrao form-localBusiness"]);
    $form->action("/admin/localBusiness/new")->method('post');
    // name
    $form->fieldsetWithInput("name", null['name'] ?? null, _("Name"));
    // description
    $form->fieldsetWithTextarea("description", null['description'] ?? null, _("Description"));
    // disambiguatingDescription
    $form->fieldsetWithTextarea("disambiguatingDescription", null['disambiguatingDescription'] ?? null, _("Disambiguating description"));
    // hasOfferCatalog
    $form->fieldsetWithInput("hasOfferCatalog", null['hasOfferCatalog'] ?? null, _("Offer catalog"));
    // url
    $form->fieldsetWithInput("url", null['url'] ?? null, "Url");
    // dateCreated
    if ("new" == "edit") $form->fieldsetWithInput("dateCreated", null['dateCreated'] ?? null, _("Date created"), "datetime", null, [ "disabled" ]);
    // dateModified
    if ("new" == "edit") $form->fieldsetWithInput("dateModified", null['dateModified'] ?? null, _("Date modified"), "datetime", null, [ "disabled" ]);
    // submit buttons
    $form->submitButtonSend();
    if ("new" == "edit") $form->submitButtonDelete("/admin/localBusiness/erase");
    // ready
    return $form->ready();
  }
}
