<?php
namespace Plinct\Cms\View\WebSite\Type\Person;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;

abstract class PersonAbstract
{
  /**
   * @var array
   */
  protected array $content;
  /**
   * @var string
   */
  protected string $name = '';
	/**
	 * @var int
	 */
	protected int $idperson;

	/**
   *
   */
  public static function navbarIndex(): void
  {
		CmsFactory::View()->addHeader(
			CmsFactory::View()->fragment()->navbar()
        ->type('person')
        ->title(_("Person"))
        ->newTab('/admin/person', CmsFactory::View()->fragment()->icon()->home())
        ->newTab('/admin/person/new', CmsFactory::View()->fragment()->icon()->plus())
        ->search()
        ->ready()
      );
  }

	/**
	 * @param string $name
	 * @param int $idperson
	 * @return void
	 */
  public static function navbarEdit(string $name, int $idperson): void
  {
    // LEVEL 1
    self::navbarIndex();
    // LEVEL 2
    CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
      ->type('person')
      ->title($name)
      ->level(3)
      ->newTab("/admin/person/edit/$idperson", CmsFactory::view()->fragment()->icon()->home())
      ->ready()
    );
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formPerson(string $case = 'new', $value = null): array
  {
		$givenName = $value['givenName'] ?? null;
		$familyName = $value['familyName'] ?? null;
		$additionalName = $value['additionalName'] ?? null;
		$taxId = $value['taxId'] ?? null;
		$birthDate = $value['birthDate'] ?? null;
		$birthPlace = $value['birthPlace'] ?? null;
		$deathDate = $value['deathDate'] ?? null;
		$deathPlace = $value['deathPlace'] ?? null;
		$gender = $value['gender'] ?? null;
		$hasOccupation = $value['hasOccupation'] ?? null;
		// FORM
    $form = CmsFactory::view()->fragment()->form("form-person", ["class" => "form-basic form-person"]);
    $form->action("/admin/person/$case")->method('post');
		// HIDDEN
		if ($case === 'edit') {
			$form->input('idperson', (string) $this->idperson, 'hidden');
		}
		// THING
		$form = Thing::formContent($form, $value);
		// GIVEN NAME
	  $form->fieldsetWithInput('givenName', $givenName, _("Given name") );
	  // FAMILY NAME
	  $form->fieldsetWithInput('familyName', $familyName, _("Family name"));
		// ADDITIONAL NAME
	  $form->fieldsetWithInput('additionalName', $additionalName, _("Additional name"));
	  // GENDER
	  $form->fieldsetWithInput('gender', $gender, _("Gender"));
		// TAX ID
	  $form->fieldsetWithInput('taxId', $taxId, _("Tax ID"));
	  // HAS OCCUPATION
	  $form->fieldsetWithInput('hasOccupation', $hasOccupation, _("Has occupation"));
	  // BIRTHPLACE
	  $form->fieldsetWithInput('birthPlace', $birthPlace, _("Birth place"));
		// BIRTH DATA
	  $form->fieldsetWithInput('birthDate', $birthDate, _("Birth date"), 'date');
	  // DEATH PLACE
	  $form->fieldsetWithInput('deathPlace', $deathPlace, _("Death place"));
	  // DEAth DATA
	  $form->fieldsetWithInput('deathDate', $deathDate, _("Death date"), 'date');
		// SUBMIT
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete("/admin/person/erase");
    return $form->ready();
  }
}
