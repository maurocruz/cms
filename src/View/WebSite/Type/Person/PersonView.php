<?php
namespace Plinct\Cms\View\WebSite\Type\Person;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PostalAddressView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;

class PersonView extends ThingView
{
	/**
	 * @var string|null
	 */
	private ?string $idperson = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'person', string $sitemapFilename = 'sitemap-person.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();

		CmsFactory::View()->addHeader(
			CmsFactory::View()->fragment()->navbar()
				->type('person')
				->title(_("Person"))
				->newTab('/admin/person', CmsFactory::View()->fragment()->icon()->home())
				->newTab('/admin/person/new', CmsFactory::View()->fragment()->icon()->plus())
				->newTab('/admin/person/sitemap', CmsFactory::View()->fragment()->icon()->sitemap())
				->search()
				->ready()
		);

		if ($this->idthing && $this->name && $this->idperson) {
			$navbar = CmsFactory::View()->fragment()->navbar()
				->type('person')
				->title($this->name)
				->level(3)
				->newTab("/admin/person/edit/$this->idperson", CmsFactory::View()->fragment()->icon()->home());
			// ROLE
			if (in_array("Role",CmsFactory::controller()->configuration()->getModulesEnabled())) {
				$navbar->newTab("/admin/role?refererType=Person&refererName=$this->name&refererId=$this->idperson&refererIdthing=$this->idthing", _('Roles'));
			}
			// CERTIFICATION
			if (in_array("Certification",CmsFactory::controller()->configuration()->getModulesEnabled())) {
				$navbar->newTab("/admin/certification?about=$this->idthing", _('Certifications'));
			}
			// READY
			CmsFactory::View()->addHeader(
				$navbar->ready()
			);
		}
	}

	/**
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function index(?array $data, array $queryParams = null): void
  {
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('person')->ready()
		);
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @param
   */
  public function new(?array $data, array $queryParams = null): void
  {
    CmsFactory::View()->addMain(
			CmsFactory::View()->fragment()->box()->simpleBox(self::formPerson(),_("Add new"))
    );
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
    if (!empty($data)) {
			if (isset($data[0])) {
				$value = $data[0];
				$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
				$this->idperson = $typeBuilder->getId();
				$this->idthing = $typeBuilder->getIdthing();
				$this->name = $value['name'];
				$address = $value['address'] ?? null;
				// FORM
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formPerson('edit', $value), _("Edit person"), $this->idthing, $this->name));
				// CONTACT POINT
				CmsFactory::view()->addMain(
					CmsFactory::view()->fragment()->reactShell('contactPoint')->setIdHasPart($this->idthing)->ready()
				);
				//CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), ContactPointView::getForm('person', $this->idthing, $value['contactPoint'] ?? null)));
				// ADDRESS
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Postal address"), PostalAddressView::formPostalAddress('person',$this->idperson, $address ? 'edit' : 'new', $address)));
				// IMAGE OBJECT
				CmsFactory::view()->addMain(
					CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty("hasPart")->ready());
			}
			elseif (isset($data['status'])) {
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['status'].": ".$data['message']));
			}
    } else {
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Person is not exists!")));
    }
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
		$form = parent::formThingContent($form, $value);
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
