<?php
namespace Plinct\Cms\View\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class Organization extends OrganizationAbstract implements TypeViewInterface
{
	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('organization')->ready()
		);
	}
  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @param
   */
  public function new(?array $data, array $queryParams = null): void
  {
    // NAVBAR
    parent::navbarNew();
    //
    CmsFactory::View()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox( self::formOrganization(), _("Add organization"))
    );
  }
  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		// NAVBAR
		if (isset($data['status']) && $data['status']=='fail') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
		} elseif (!empty($data)) {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			$this->idorganization =  $typeBuilder->getId();
			$this->idthing = $typeBuilder->getPropertyValue('idthing');
			$this->name = $value['name'];
			// NAVBAR
			parent::navbarEdit($this->name, $this->idorganization, $this->idthing);
			// ORGANIZATION
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Organization"), self::formOrganization('edit', $value), true)
			);
			// CONTACT POINT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('organization', $this->idthing, $value['contactPoint'] ?? null))
			);
			// IMAGE
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $this->idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Organization is not exists!")));
		}
  }
}
