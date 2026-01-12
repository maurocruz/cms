<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;

class ReviewView extends CreativeWorkView
{
	private string $idreview = '';

	/**
	 * ReviewView constructor.
	 */
	public function __construct(string $type = 'review', string $sitemapFilename = 'sitemap-review.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();

		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('review')
			->setTitle(_('Review'))
			->newTab('/admin/review',  CmsFactory::view()->fragment()->icon()->home())
			->newTab('/admin/review/new',  CmsFactory::view()->fragment()->icon()->plus())
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
			CmsFactory::view()->fragment()->reactShell('review')->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 * */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formReview(),_('Add new')." "._('review'))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 * */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			$tb = CmsFactory::helpers()->typeBuilder($value);
			$this->idreview = $tb->getId();
			$this->name = $tb->getValue('name');
			$this->idthing = $tb->getIdthing();
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formReview('edit', $value), _('review'), $this->idthing,$this->name));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No review found!'));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 * */
	public function formReview(string $case = 'new', array $value = null): array
	{
		$itemReviewed = $value['itemReviewed'] ?? null;
		$form = CmsFactory::view()->fragment()->form('form-review',['class'=>'form-basic form-review']);
		$form->action("/admin/review/$case")->method('post');
		$form->addMandatories('itemReviewed','reviewAspect','reviewBody','reviewRating');
		// PARENT TYPE
		$form = parent::formCreativeWorkContent($form, $value);
		// ITEM REVIEWED
		$typesEnabled = implode(',',CmsFactory::controller()->configuration()->getModulesAvailable());
		$form->relationshipOneToOne($typesEnabled,_('Item reviewed'), 'itemReviewed', $itemReviewed);
		// REVIEW ASPECT
		$form->fieldsetWithInput('reviewAspect', $value['reviewAspect'] ?? null, _('Review aspect'));
		// REVIEW BODY
		$form->fieldsetWithTextarea('reviewBody', $value['reviewBody'] ?? null, _('Review description'));
		// REVIEW RATING
		$form->fieldsetWithRadio('reviewRating',['1'=>_('Poor'), '2'=>_('Fair'), '3'=>_('Good'),'4'=>_('Very good'),'5'=>_('Excellent')], $value['reviewRating'] ?? null, _('Rating'));
		$form->submitButtonSend();
		return $form->ready();
	}
}
