<?php
namespace Plinct\Cms\View\WebSite\Type\Product;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class Product extends ProductAbstract implements TypeViewInterface
{

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		$tb = ToolBox::typeBuilder($data);
		$this->manufacturer = $tb->getPropertyValue('idthing');
		if ($tb->getType() == 'Organization') {
			Organization::navbarIndex();
			Organization::navbarEdit($tb->getValue('name'), $tb->getId(), $this->manufacturer);
		}
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('product')->setIdHasPart((int) $this->manufacturer)->ready()
		);
	}

	public function new(?array $data, array $queryParams = null): void
	{
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->form())
		);
	}

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit($data, array $queryParams = null): void
  {
    $value = $data[0];
	  parent::navbarIndex($value['name']);
		$typeBuilder = ToolBox::typeBuilder($value);
		$idthing = $typeBuilder->getPropertyValue('idthing');
		CmsFactory::view()->addMain([
				CmsFactory::view()->fragment()->box()->simpleBox($this->form($value)),
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int) $idthing)->ready()
			]
		);
  }

	private function form(?array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-product");
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
		$form = ThingView::formContent($form, $value);
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
