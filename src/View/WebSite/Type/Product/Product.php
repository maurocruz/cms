<?php
namespace Plinct\Cms\View\WebSite\Type\Product;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class Product extends ProductAbstract implements TypeViewInterface
{

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value): void
	{
		$tb = ToolBox::typeBuilder($value);
		$this->manufacturer = $tb->getPropertyValue('idthing');
		if ($tb->getType() == 'Organization') {
			Organization::navbarIndex();
			Organization::navbarEdit($tb->getValue('name'), $tb->getId(), $this->manufacturer);
		}
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('product')->setHasPart((int) $this->manufacturer)->ready()
		);
	}

	public function new(?array $value): void
	{
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->form())
		);
	}

  /**
   * @throws Exception
   */
  public function edit($data): void
  {
    $value = $data[0];
	  parent::navbarIndex($value['name']);
		$typeBuilder = ToolBox::typeBuilder($value);
		$idthing = $typeBuilder->getPropertyValue('idthing');
		CmsFactory::view()->addMain([
				CmsFactory::view()->fragment()->box()->simpleBox($this->form($value)),
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf((int) $idthing)->ready()
			]
		);
  }

	private function form(?array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form();
		$form->attributes(['class'=>'form-basic form-product']);
		$form->method('post');
		if ($value) {
			$typeBuilder = ToolBox::typeBuilder($value);
			$idproduct = $typeBuilder->getId();
			$form->action('/admin/product/edit');
			$form->input('idproduct', (string) $idproduct, 'hidden');
		} else {
			$form->action('/admin/product/new');
		}
		$form = Thing::formContent($form, $value);
		// category
		$form->fieldsetWithInput('category',$value['category'] ?? null, _('Category'));
		// manufacturer
		$form->relationshipOneToOne('Organization',_('Manufacturer'),'manufacturer',$value['manufacturer'] ?? null);

		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/product/delete');
		}
		return $form->ready();
	}
}
