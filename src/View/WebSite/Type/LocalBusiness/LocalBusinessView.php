<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;

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
		$apiHost = App::getApiHost();
    $this->navbarLocalBussines();
		CmsFactory::webSite()->addMain("<div class='plinct-shell' data-type='localBusiness' data-apihost='$apiHost'></div>");
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
		$apiHost = App::getApiHost();
		$userToken = CmsFactory::request()->user()->userLogged()->getToken();
		// NAVBAR
    $this->navbarLocalBussines($value['name']);
    // LOCAL BUSINESS
		CmsFactory::webSite()->addMain("<div
			class='plinct-shell'
			data-type='localBusiness'
			data-idispartof='{$value['idlocalBusiness']}'
			data-apihost='{$apiHost}'
			data-usertoken='{$userToken}'
		></div>");
    //$content[] = "<script src='https://plinct.com.br/static/dist/plinct-thing/main.js'></script>";
    //$content[] = "<div id='plinctThing' data-type='LocalBusiness' data-id='$id' data-apiHost='$apiHost'></div>";

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
		/*$content[] = "<div
			class='plinct-shell'
			data-type='imageObject'
			data-tablehaspart='localBusiness'
			data-idhaspart='{$value['idlocalBusiness']}'
			data-apihost='{$apiHost}'
			data-usertoken='{$userToken}'
		></div>";*/
    //$content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("localBusiness", $id, $value['image']));

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
