<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Person;

use Plinct\Cms\CmsFactory;

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
  protected function navbarPerson()
  {
		CmsFactory::View()->addHeader(
			CmsFactory::View()->fragment()->navbar()
        ->type('person')
        ->title(_("Person"))
        ->newTab('/admin/person', CmsFactory::View()->fragment()->icon()->home())
        ->newTab('/admin/person/new', CmsFactory::View()->fragment()->icon()->plus())
        ->level(2)
        ->search('/admin/person')
        ->ready()
      );
  }

  protected function navbarPersonEdit()
  {
    // LEVEL 1
    $this->navbarPerson();
    // LEVEL 2
    CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
      ->type('person')
      ->title($this->name)
      ->level(3)
      ->newTab("/admin/person/edit/$this->idperson", CmsFactory::view()->fragment()->icon()->home())
      //->newTab("/admin/person?id=$this->id&action=service", _("Services"))
     // ->newTab("/admin/person?id=$this->id&action=product", _("Products"))
      ->ready()
    );
  }

  /**
   * @param string $case
   * @param null $value
   * @param null $tableHasPart
   * @param null $idHasPart
   * @return array
   */
  protected function formPerson(string $case = 'new', $value = null, $tableHasPart = null, $idHasPart = null): array
  {
		$name = $value['name'] ?? null;
		$description = $value['description'] ?? null;
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$url = $value['url'] ?? null;
		$givenName = $value['givenName'] ?? null;
		$familyName = $value['familyName'] ?? null;
		$additionalName = $value['additionalName'] ?? null;
		$taxId = $value['familyName'] ?? null;
		$birthDate = $value['birthDate'] ?? null;
		$birthPlace = $value['birthPlace'] ?? null;
		$deathDate = $value['deathDate'] ?? null;
		$deathPlace = $value['deathPlace'] ?? null;
		$gender = $value['gender'] ?? null;
		$hasOccupation = $value['hasOccupation'] ?? null;
		$homeLocation = $value['homeLocation'] ?? null;

    $form = CmsFactory::view()->fragment()->form(["class" => "form-basic form-person"]);
    $form->action("/admin/person/$case")->method('post');
		// HIDDEN
		if ($case === 'edit') {
			$form->input('idperson', (string) $this->idperson, 'hidden');
		}
		// NAME
	  $form->fieldsetWithInput('name',$name,_("Name").'*');
		// DESCRIPTION
	  $form->fieldsetWithTextarea('description', $description, _('Description'));
		// DISAMBIGUATING DESCRIPTION
	  $form->fieldsetWithTextarea('disambiguatingDescription', $disambiguatingDescription, _('Disambiguating description'));
		// URL
	  $form->fieldsetWithInput('url', $url, "Url");
		// GIVEN NAME
	  $form->fieldsetWithInput('givenName', $givenName, _("Given Name") );
	  // FAMILY NAME
	  $form->fieldsetWithInput('familyName', $familyName, _("Family name"));
		// ADDITIONAL NAME
	  $form->fieldsetWithInput('additionalName', $additionalName, _("Additional name"));
		// TAX ID
	  $form->fieldsetWithInput('taxId', $taxId, _("Tax ID"));
		// BIRTH DATA
	  $form->fieldsetWithInput('birthDate', $birthDate, _("Birth data"), 'date');
		// BIRTHPLACE
	  $form->fieldsetWithInput('birthPlace', $birthPlace, _("Birth place"));
	  // DEAth DATA
	  $form->fieldsetWithInput('deathDate', $deathDate, _("Death data"), 'date');
	  // DEATH PLACE
	  $form->fieldsetWithInput('deathPlace', $deathPlace, _("Death place"));
	  // GENDER
	  $form->fieldsetWithInput('gender', $gender, _("Gender"));
	  // HAS OCCUPATION
	  $form->fieldsetWithInput('hasOccupation', $hasOccupation, _("Has occupation"). " <span style='font-size: 85%;'>("._("Separate with semicolons if there are many").")</span>");
		// SUBMIT
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete("/admin/person/erase");
    return $form->ready();
  }
}
