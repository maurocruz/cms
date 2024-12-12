<?php
namespace Plinct\Cms\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\Intangible\ContactPoint;

class LocalBusinessView
{
  /**
   *
   */
  public function navbarLocalBussines(string $title = null)
  {
    CmsFactory::webSite()->navbar(_("Locals business"), [
        "/admin/localBusiness" => CmsFactory::response()->fragment()->icon()->home(16,16),
        "/admin/localBusiness/new" => CmsFactory::response()->fragment()->icon()->plus(16,16)
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
		$content[] = CmsFactory::response()->fragment()->box()->simpleBox(self::formLocalBussiness("edit", $value), _("LocalBusiness"));
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
		$content[] = "<div
			class='plinct-shell'
			data-type='imageObject'
			data-tablehaspart='localBusiness'
			data-idhaspart='{$value['idlocalBusiness']}'
			data-apihost='{$apiHost}'
			data-usertoken='{$userToken}'
		></div>";

    CmsFactory::webSite()->addMain($content);
  }

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
  private static function formLocalBussiness($case = 'new', array $value = null): array
  {
		$idlocalBusiness = $value['idlocalBusiness'] ?? null;
		$name = $value['name'] ?? null;
		$description = $value['description'] ?? null;
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$hasOfferCatalog = $value['hasOfferCatalog'] ?? null;
		$url = $value['url'] ?? null;
		$dateCreated = $value['dateCreated'] ?? null;
		$dateModified = $value['dateModified'] ?? null;

    $form = CmsFactory::response()->fragment()->form(["id"=>"form-localBusiness", "class" => "formPadrao form-localBusiness"]);
    $form->action("/admin/localBusiness/$case")->method('post');
		if ($idlocalBusiness) $form->input('idlocalBusiness', $idlocalBusiness, 'hidden');
    // name
    $form->fieldsetWithInput("name", $name, _("Name"));
    // description
    $form->fieldsetWithTextarea("description", $description, _("Description"));
    // disambiguatingDescription
    $form->fieldsetWithTextarea("disambiguatingDescription", $disambiguatingDescription, _("Disambiguating description"));
    // hasOfferCatalog
    $form->fieldsetWithInput("hasOfferCatalog", $hasOfferCatalog, _("Offer catalog"));
    // url
    $form->fieldsetWithInput("url", $url, "Url");
    // dateCreated
    if ("new" == "edit") $form->fieldsetWithInput("dateCreated", $dateCreated, _("Date created"), "datetime", null, [ "disabled" ]);
    // dateModified
    if ("new" == "edit") $form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"), "datetime", null, [ "disabled" ]);
    // submit buttons
    $form->submitButtonSend();
    if ($idlocalBusiness) $form->submitButtonDelete("/admin/localBusiness/erase");
    // ready
    return $form->ready();
  }
}
