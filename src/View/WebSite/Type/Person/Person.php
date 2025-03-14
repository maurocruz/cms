<?php
namespace Plinct\Cms\View\WebSite\Type\Person;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\Certification;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Person extends PersonAbstract implements TypeViewInterface
{
  /**
   * @param ?array $value
   */
  public function index(?array $value): void
  {
    $this->navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('person')->ready()
		);
  }

  /**
   * @param array|null $value
   * @param
   */
  public function new(?array $value): void
  {
      $this->navbarIndex();
      CmsFactory::View()->addMain(
				CmsFactory::View()->fragment()->box()->simpleBox(self::formPerson(),_("Add new"))
      );
  }

  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data): void
  {
    if (!empty($data)) {
      $value = $data[0];
	    $typeBuilder = new TypeBuilder('person', $value);
      $this->idperson = (int) $typeBuilder->getId();
			$this->name = $value['name'];
			$idthing = (int) $typeBuilder->getPropertyValue('idthing');
      // NAVBAR
      $this->navbarEdit($this->name, $this->idperson);
      // FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox( _("Edit person"), self::formPerson('edit', $value), true));
			// HAS CERTIFICATION
	    CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Certification"), Certification::hasCertification($value))
	    );
      // IMAGE
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdIsPartOf($idthing)->ready());
	    // CONTACT POINT
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('person', $this->idperson, $value['contactPoint'])));

    } else {
      $this->navbarIndex();
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Person is not exists!")));
    }
  }
}
