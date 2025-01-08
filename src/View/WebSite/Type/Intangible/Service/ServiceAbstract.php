<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Service;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Tool\ToolBox;

abstract class ServiceAbstract
{
	/**
	 * @var ?string
	 */
	protected ?string $provider = null;

	/**
	 *
	 */
	protected function navbarIndex()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_("Services"))
				->type('service')
				->level(4)
				->newTab("/admin/service?provider=$this->provider", CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab("/admin/service/new?provider=$this->provider", CmsFactory::view()->fragment()->icon()->plus(16,16))
				->ready()
		);
	}

	protected function navbaredit($title)
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
			->title($title)
			->type('service')
			->level(5)
			->ready()
		);
	}
  /**
   * @return array
   */
  protected function newWithPartOfForm(): array
  {
    return self::serviceForm();
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function serviceForm(string $case = "new", $value = null): array
  {
		if ($value) {
			$TbService = ToolBox::typeBuilder($value);
			$idservice = $TbService->getId();
		}
    $form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-service box']);
    $form->action("/admin/service/$case")->method("post");
    // title
    $title = $case == "edit" ? _('Edit service') : _('Add new service');
    $form->content("<h4>"._($title)."</h4>");
    // HIDDENS
    //$form->input('provider',$this->provider,'hidden');
    if ($case == 'edit') $form->input('idservice', $idservice,'hidden');
		// THING
		$form = Thing::formContent($form, $value);
    // CATEGORY
    $form->fieldsetWithInput('category',$value['category'] ?? null, _('Category'));
    // TERMS OF SERVICE
    $form->fieldsetWithTextarea('termsOfService', $value['termsOfService'] ?? null, _("Terms of service"));
		// PROVIDER
	  $form->relationshipOneToOne('Organization|Person',_('Provider'),'provider', $this->provider);
    // SUBMIT BUTTONS
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/service/erase");
    // RENDER
    return $form->ready();
  }
}
