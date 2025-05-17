<?php
namespace Plinct\Cms\View\WebSite\Type\Person;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\Certification;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\Intangible\PostalAddressView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Person extends PersonAbstract implements TypeViewInterface
{
  /**
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function index(?array $data, array $queryParams = null): void
  {
    $this->navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('person')->ready()
		);
  }

  /**
   * @param array|null $value
   * @param array|null $queryParams
   * @param
   */
  public function new(?array $value, array $queryParams = null): void
  {
      $this->navbarIndex();
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
				$idthing = $typeBuilder->getIdthing();
				$this->name = $value['name'];
				$address = $value['address'] ?? null;
				// NAVBAR
				$this->navbarEdit($this->name, $this->idperson, $idthing);
				// FORM
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Edit person"), self::formPerson('edit', $value), true));
				// CONTACT POINT
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('person', $idthing, $value['contactPoint'] ?? null)));
				// ADDRESS
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Postal address"), PostalAddressView::formPostalAddress('person',$this->idperson, $address ? 'edit' : 'new', $address)));
				// HAS CERTIFICATION
				if(CmsFactory::controller()->configuration()->hasModulesAvailable('Certification')) {
					CmsFactory::view()->addMain(
						CmsFactory::view()->fragment()->box()->expandingBox(_("Certification"), Certification::hasCertification($value))
					);
				}
				// IMAGE
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready());

			} elseif (isset($data['status'])) {
				var_dump($data);
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['status'].": ".$data['message']));
			}
    } else {
      $this->navbarIndex();
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Person is not exists!")));
    }
  }
}
