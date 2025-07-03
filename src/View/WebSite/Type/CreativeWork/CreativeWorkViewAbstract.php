<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;

abstract class CreativeWorkViewAbstract extends ThingView
{
	/**
	 * @var int|null
	 */
	protected ?int $idcreativeWork;
	/**
	 * @var string|null
	 */
	protected ?string $idthing;

	/**
	 * @return void
	 */
	public static function navbar(): void
	{
		$navbar = CmsFactory::view()->fragment()->navbar()
			->type('creativeWork')
			->setTitle(_('Creative work'))
			->newTab('/admin/creativeWork',  CmsFactory::view()->fragment()->icon()->home())
			->newTab('/admin/creativeWork/new',  CmsFactory::view()->fragment()->icon()->plus())
			->setModulesAvailable(['Article','Book','Certification','MediaObject','WebPage','WebPageElement','WebSite'])
			->search()
		;
		$modulesEnabled = CmsFactory::controller()->configuration()->getModulesEnabled();
		if ($modulesEnabled) {
			foreach ($modulesEnabled as $type) {
				// MEDIA OBJECT
				if (in_array($type, ['ImageObject','VideoObject'])) {
					$navbar->newTab("/admin/mediaObject", _('Media object'));
				}
			}
		}
		CmsFactory::view()->addHeader($navbar->ready());
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	protected function formCreativeWork(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-creativeWork", ['class'=>'form-basic form-creativeWork']);
		$form->method('post');
		$form->action("/admin/creativeWork/$case");
		// id
		if ($case == 'edit') {
			$form->input('idcreativeWork', (string) $this->idcreativeWork, 'hidden');
		}
		// creative properties
		$form = self::formCreativeWorkContent($case, $form, $value);
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/book/erase");
		}
		//return
		return $form->ready();
	}

	/**
	 * @param string $case
	 * @param Form $form
	 * @param array|null $value
	 * @return Form
	 */
	public static function formCreativeWorkContent(string $case, Form $form, array $value = null): Form
	{
		$alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$editor = isset($value['editor']) ? (string) $value['editor'] : null;
		$isPartOf = isset($value['isPartOf']) ? (string) $value['isPartOf'] : null;
		$maintainer = isset($value['maintainer']) ? (string) $value['maintainer'] : null;
		$position = isset($value['position']) ? (string) $value['position'] : null;
		$publisher = isset($value['publisher']) ? (string) $value['publisher'] : null;
		// thing
		$form = self::formContent($form, $value);
		// alternativeHeadline
		$form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
		// text
		$form->fieldsetWithTextarea('text', $value['text'] ?? null, _('Text'));
		// author
		$form->fieldsetWithInput('author', $value['author'] ?? null, _('Author'));
		// version
		$form->fieldsetWithInput('version', $value['version'] ?? null, _('Version'));
		// acquireLicensePage
		$form->fieldsetWithInput('acquireLicensePage', $value['acquireLicensePage'] ?? null, _('Acquire license page'));
		// copyrightHolder
		$form->fieldsetWithInput('copyrightHolder', $value['copyrightHolder'] ?? null, _('Copyright holder'));
		// editor
		if ($case === 'edit') $form->fieldsetWithInput('editor',  $editor, _('Editor'));
		// headline
		$form->fieldsetWithInput('headline', $value['headline'] ?? null, _('Headline'));
		// isPartOf
		$form->fieldsetWithInput('isPartOf', $isPartOf, _('Is part of'));
		// keywords
		$form->fieldsetWithInput('keywords', $value['keywords'] ?? null, _('Keywords'));
		// license
		$form->fieldsetWithInput('license', $value['license'] ?? null, _('License'));
		// locationCreated
		$form->fieldsetWithInput('locationCreated', $value['locationCreated'] ?? null, _('Location created'));
		// maintainer
		if ($case === 'edit') $form->fieldsetWithInput('maintainer', $maintainer, _('Maintainer'));
		// position
		if ($case === 'edit') $form->fieldsetWithInput('position', $position, _('Position'));
		// publisher
		if ($case === 'edit') $form->fieldsetWithInput('publisher', $publisher, _('Publisher'));
		// thumbnail
		$form->fieldsetWithInput('thumbnail', $value['thumbnail'] ?? null, _('Thumbnail'));
		// datePublished
		if ($case === 'edit') {
			$form->fieldsetWithInput('datePublished', $value['datePublished'] ?? null, _('Date published'), 'text', null, ['disable']);
		}
		return $form;
	}
}
