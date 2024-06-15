<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Organization extends OrganizationAbstract implements TypeInterface
{
	/**
	 * @param ?array $value
	 */
	public function index(?array $value): void
	{
		$this->navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('organization')->ready()
		);
	}
  /**
   * @param array|null $value
   * @param
   */
  public function new(?array $value)
  {
    // NAVBAR
    parent::navbarNew();
    //
    CmsFactory::View()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox( self::formOrganization(), _("Add organization"))
    );
  }
  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data)
  {
	  // NAVBAR
	  parent::navbarIndex();
		if (!empty($data)) {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('organization', $value);
			$this->idorganization = $typeBuilder->getId();
			$this->name = $value['name'];
			$idthing = $typeBuilder->getPropertyValue('idthing');
			// NAVBAR
			parent::navbarEdit();
			// ORGANIZATION
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Organization"), self::formOrganization('edit', $value), true)
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('organization', $this->idorganization, $value['contactPoint'] ?? null))
			);
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf((int) $idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Organization is not exists!")));
		}
  }
}
