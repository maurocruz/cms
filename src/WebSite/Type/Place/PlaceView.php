<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Place;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class PlaceView
{
  /**
   * @var string
   */
  protected string $placeId;

  /**
   *
   */
  public function navbarPlace(string $title = null)
  {
    CmsFactory::webSite()->navbar(_("Place"), [
        "/admin/place" => CmsFactory::response()->fragment()->icon()->home(),
        "/admin/place/new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 2, ['table'=>'place']);

    if ($title) {
        CmsFactory::webSite()->navbar($title, [], 3);
    }
  }

  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarPlace();

    $listTable = CmsFactory::response()->fragment()->listTable()
      ->caption(sprintf(_("List of %s"),_("places")))
      ->labels('id', _("Name"), _("AdditionalType"), _("Date modified"))
      ->rows($data['itemListElement'], ['idplace', 'name', 'additionalType', 'dateModified'])
      ->setEditButton('/admin/place/edit/');
    CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   * @param null $data
   */
  public function new($data = null)
  {
    $this->navbarPlace();
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formPlace(), _("Add new")));
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data)
  {
		if (empty($data)) {
			CmsFactory::webSite()->addMain("<p>"._("Nothing found!")."</p>");
		} else {
			$value = $data[0];
			$this->placeId = isset($value) ? $value['idplace'] : null;
			// NAVBAR
			$this->navbarPlace($value['name']);
			$apiHost = App::getApiHost();
			CmsFactory::webSite()->addMain("<script src='https://plinct.com.br/static/dist/plinct-place/main.1935c2813b37368c9dac.js'></script>");
			CmsFactory::webSite()->addMain("<div id='plinctPlace' data-id='{$value['idplace']}' data-apiHost='$apiHost'></div>");
		}
  }

	/**
	 * @return array
	 */
  private function formPlace(): array
  {
    $form = CmsFactory::response()->fragment()->form([ "id" => "form-place-new", "name" => "place-form-new", "class" => "formPadrao form-place" ]);
    $form->action("/admin/place/new")->method("post");
    // name
    $form->fieldsetWithInput("name", $value['name'] ?? null, _("Place"));
    $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
    // disambiguating description
    $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
    // submit
    $form->submitButtonSend();
    return $form->ready();
  }
}
