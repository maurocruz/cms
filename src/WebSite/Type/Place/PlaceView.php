<?php
namespace Plinct\Cms\WebSite\Type\Place;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class PlaceView
{
  /**
   * @var string|int
   */
  protected string|int $placeId;

  /**
   *
   */
  public function navbarPlace(string $title = null): void
  {
    CmsFactory::webSite()->navbar(_("Place"), [
        "/admin/place" => CmsFactory::response()->fragment()->icon()->home(16,16),
        "/admin/place/new" => CmsFactory::response()->fragment()->icon()->plus(16,16)
    ], 2, ['table'=>'place']);

    if ($title) {
        CmsFactory::webSite()->navbar($title, [], 3);
    }
  }

	/**
	 */
  public function index(): void
  {
    $this->navbarPlace();
		CmsFactory::webSite()->addMain('<div class="plinct-shell" data-type="place" data-tablehaspart="place" data-apihost="'.App::getApiHost().'"></div>');
  }

  /**
   * @param null $data
   */
  public function new($data = null): void
  {
    $this->navbarPlace();
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formPlace(), _("Add new")));
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data): void
  {
		if (empty($data)) {
			CmsFactory::webSite()->addMain("<p>"._("Nothing found!")."</p>");
		} else {
			$value = $data[0];
			$apiHost = App::getApiHost();
			$this->placeId = isset($value) ? (string) $value['idplace'] : null;
			// NAVBAR
			$this->navbarPlace($value['name']);
			CmsFactory::webSite()->addMain("<div class='plinct-shell' data-type='place' data-idIsPartOf='{$value['idplace']}' data-apiHost='$apiHost'  data-openSection='true'></div>");
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
