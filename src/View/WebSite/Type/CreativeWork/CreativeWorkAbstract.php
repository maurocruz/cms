<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;

abstract class CreativeWorkAbstract
{
	protected ?int $idcreativeWork;

	public static function navbar()
	{
		$navbar = CmsFactory::view()->fragment()->navbar()
			->type('creativeWork')
			->setTitle(_('Creative work'))
			->newTab('/admin/creativeWork',  CmsFactory::view()->fragment()->icon()->home(18,18))
			->newTab('/admin/creativeWork/new',  CmsFactory::view()->fragment()->icon()->plus(18,18))
			->search('/admin/creativeWork')
		;
		$subclass = App::getTypesEnabled()['CreativeWork'] ?? null;
		if ($subclass) {
			foreach ($subclass as $type) {
				$navbar->newTab("/admin/".lcfirst($type), _($type));
			}
		}
		CmsFactory::view()->addHeader($navbar->ready());
	}

	protected function form(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-creativeWork']);
		$form->method('post');
		$form->action("/admin/creativeWork/$case");
		// id
		if ($case == 'edit') {
			$form->input('idcreativeWork', (string) $this->idcreativeWork, 'hidden');
		}
		// creative properties
		$form = self::formContent($case, $form, $value);
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/book/erase");
		}
		//return
		return $form->ready();
	}

	public static function formContent(string $case, Form $form, array $value = null): Form
	{
		// thing
		$form = Thing::formContent($form, $value);
		// alternativeHeadline
		$form->fieldsetWithInput('alternativeHeadline', $value['alternativeHeadline'] ?? null, _('Alternative headline'));
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
		if ($case === 'edit') $form->fieldsetWithInput('editor',  (string) $value['editor'] ?? null, _('Editor'));
		// headline
		$form->fieldsetWithInput('headline', $value['headline'] ?? null, _('Headline'));
		// isPartOf
		$form->fieldsetWithInput('isPartOf', (string) $value['isPartOf'] ?? null, _('Is part of'));
		// keywords
		$form->fieldsetWithInput('keywords', $value['keywords'] ?? null, _('Keywords'));
		// license
		$form->fieldsetWithInput('license', $value['license'] ?? null, _('License'));
		// locationCreated
		$form->fieldsetWithInput('locationCreated', $value['locationCreated'] ?? null, _('Location created'));
		// maintainer
		if ($case === 'edit') $form->fieldsetWithInput('maintainer', (string) $value['maintainer'] ?? null, _('Maintainer'));
		// position
		if ($case === 'edit') $form->fieldsetWithInput('position', (string) $value['position'] ?? null, _('Position'));
		// publisher
		if ($case === 'edit') $form->fieldsetWithInput('publisher', (string) $value['publisher'] ?? null, _('Publisher'));
		// thumbnail
		$form->fieldsetWithInput('thumbnail', $value['thumbnail'] ?? null, _('Thumbnail'));
		// datePublished
		if ($case === 'edit') {
			$form->fieldsetWithInput('datePublished', $value['datePublished'] ?? null, _('Date published'), 'text', null, ['disable']);
		}
		return $form;
	}
}