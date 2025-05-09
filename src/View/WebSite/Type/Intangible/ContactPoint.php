<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class ContactPoint implements TypeViewInterface
{
	public static function navbar(): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Contact point'))
				->type('ContactPoint')
				->level(3)
				->newTab('/admin/contactPoint', CmsFactory::view()->fragment()->icon()->home(16,16))
				->ready()
		);
	}

	public function index(?array $data, array $queryParams = null): void
	{
		Intangible::navbar();
		self::navbar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('contactPoint')->ready()
		);
	}

	public function edit(?array $data, array $queryParams = null): void
	{
		Intangible::navbar();
	}

	public function new(?array $value, array $queryParams = null)
	{
		// TODO: Implement new() method.
	}

  /**
   * @param $tableHasPart
   * @param $idHasPart
   * @param $data
   * @return array
   */
  public function getForm($tableHasPart, $idHasPart, $data): array
  {
    if ($data) {
      foreach ($data as $key => $value) {
        $content[] = self::formContactPoint($tableHasPart, $idHasPart, "edit", $value);
        $content[] = [ "tag" => "hr" ];
      }
    }
    $pos = isset($key) ? ($key+2) : 1;
    $content[] = self::formContactPoint($tableHasPart, $idHasPart, "new", null, $pos );
    return $content;
  }

	/**
	 * @param string $typeHasPart
	 * @param $idHasPart
	 * @param string $case
	 * @param null $value
	 * @param null $key
	 * @return array
	 */
  static private function formContactPoint(string $typeHasPart, $idHasPart, string $case = 'new', $value = null, $key = null): array
  {
		if ($value) {
			$typeBuilder = new TypeBuilder('contactPoint', $value);
			$idcontactPoint = $typeBuilder->getId();
		}
    $form = CmsFactory::view()->fragment()->form("form-contactPoint",["class" => "form-basic form-contactPoint"]);
    $form->action("/admin/contactPoint/$case")->method("post");
    // hiddens
    $form->input('typeHasPart', $typeHasPart, "hidden");
    if ($case === "new") {
      $form->input('idHasPart', (string) $idHasPart, "hidden");
      $form->content("<h4>"._('New').": </h4>");
    } elseif ($value) {
      $form->input('idcontactPoint', (string) $idcontactPoint, 'hidden');
      $form->input('idIsPartOf', (string) $idcontactPoint, 'hidden');
    }
    // POSITION
    $form->fieldsetWithInput("position", (isset($value['position']) ? (string) $value['position'] : (string) $key), "#", "number", null, [ "min" => "1"]);
    // NAME
    $form->fieldsetWithInput("name", $value['name'] ?? null, _("Contact name"));
    // CONTACT TYPE
    $form->fieldsetWithInput("contactType", $value['contactType'] ?? null, _("Contact type"));
    // TELEPHONE
    $form->fieldsetWithInput("telephone", $value['telephone'] ?? null, _("Telephone"));
    // WHATSAPP
    $whatsapp = isset($value['whatsapp']) ? (int) $value['whatsapp'] : 0;
		$form->fieldsetWithRadio('whatsapp', [1=>'Sim',0=>'Não'], $whatsapp, _("Whatsapp"));

    // EMAIL
    $form->fieldsetWithInput("email", $value['email'] ?? null, _("Email"));
    // OBS
    $form->fieldsetWithInput("obs", $value['obs'] ?? null, _("Obs"));
    // SUBMIT
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/contactPoint/erase");
    // READY
    return $form->ready();
  }
}
