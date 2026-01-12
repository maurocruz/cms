<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class CollectionView extends CreativeWorkView implements TypeViewInterface
{
	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'collection', string $sitemapFilename = 'sitemap-collection.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('collection')
				->title(_('Collection'))
				->level(3)
				->newTab('/admin/collection', CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/collection/new', CmsFactory::view()->fragment()->icon()->plus())
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('collection')->setColumnsTable(['@type'=>_('Types')])->ready()
		);
	}

	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formCollection(),_('Add new')." "._('collection'))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if(isset($data[0])) {
			$value = $data[0];
			$tbCollection = CmsFactory::toolBox()->typeBuilder($value);
			$this->idthing = $tbCollection->getPropertyValue('idthing');
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formCollection('edit', $data[0]), _('Edit') . " " . _('collection'))
			);
			// HAS PART REACT
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('collection')->setProperty('hasPart')->setIdHasPart($this->idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No collection found!'));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function formCollection(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form('form-collection',['class'=>'form-basic form-collection']);
		$form->action("/admin/collection/$case")->method('post');
		if ($value) {
			$tbCollection = CmsFactory::toolBox()->typeBuilder($value);
			$idcollection = $tbCollection->getId();
			$form->input('idcollection',(string) $idcollection,'hidden');
		}
		// CREATIVE WORK FORM
		$form = parent::formCreativeWorkContent($form, $value);
		// SUBMIT
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/collection/erase');
		}
		return $form->ready();
	}

	/**
	 * Creates and returns a form for managing the "hasPart" relationship in a collection.
	 *
	 * @return array The form configuration as an array.
	 */
	private function formCollectionHasPart(): array
	{
		$form = CmsFactory::view()->fragment()->form('form-collectionHasPart',['class'=>'form-basic form-collectionHasPart']);
		$form->action("/admin/collection/new")->method('post');
		$form->input('idhasPart',(string) $this->idthing,'hidden');
		$form->fieldsetWithInput('uploadfile[]', null, _('Upload files'), 'file');
		$form->fieldsetWithInput('destination', null, _('Destination folder'), 'text', null, array('placeholder' => 'ex: /folder/subfolder/'));
		$form->fieldsetWithInput('keywords', null, _('Keywords'));
		$form->relationshipOneToOne('CreativeWork,MediaObject,AudioObject,ImageObject,VideoObject',_('Files on the server'), 'idisPartOf');
		$form->submitButtonSend();
		return $form->ready();
	}
}
