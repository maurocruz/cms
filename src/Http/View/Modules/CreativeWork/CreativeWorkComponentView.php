<?php
namespace Plinct\Cms\Http\View\Modules\CreativeWork;

use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Contracts\ModuleComponentViewInterface;
use Plinct\Cms\Http\View\Modules\Thing\ThingComponentView;

class CreativeWorkComponentView implements ModuleComponentViewInterface
{
	public static function navbar(array $querystrings = null): array
	{
		$navbar = ComponentFactory::navbar()
			->type('creativeWork')
			->setTitle(_('Creative work'))
			->newTab('/admin/creativeWork',  ComponentFactory::icon()->home())
			->newTab('/admin/creativeWork/new',  ComponentFactory::icon()->plus())
			->setModulesAvailable(['Article','Book','Certification','Collection','MediaObject','Review','WebPage','WebPageElement','WebSite'])
			->search();
		return $navbar->ready();
	}

	public static function navbarItem(string $name, string $id, array $querystrings = null): array
	{
		return [];
	}

	public static function navbarParent(string $name, string $id, string $nameParent, string $idparent, array $queryStrings = null): array
	{
		return [];
	}

	public static function form(Form $form, string $case = 'new', array $value = null, string $idcreativeWork = null): array
	{
		// FORM
		$form->method('post');
		$form->action("/admin/creativeWork/$case");
		// id
		if ($idcreativeWork) {
			$form->input('idcreativeWork', $idcreativeWork, 'hidden');
		}
		// creativeWork form
		self::formFragment($form, $value);
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/creativeWork/erase");
		}
		//return
		return $form->ready();
	}

	public static function formFragment(Form $form, array $value = null, $idIsPartOf = null, string $type = null): Form
	{
		$isPartOf = $idIsPartOf ?? null;
		$alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$position = isset($value['position']) ? (string) $value['position'] : null;
		$publisher = isset($value['publisher']) ? (string) $value['publisher'] : null;
		$editor = isset($value['editor']) ? (string) $value['editor'] : null;
		$datePublished = $value['datePublished'] ?? null;
		$creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		// thing
		$form = ThingComponentView::formFragment($form, $value);

		if ($type !== 'CreativeWork') {
			$form->content(ComponentFactory::box()->expandingBoxWithoutContent(_("Creative work"), "form-creativeWork", !$value));
		}
		// isPartOf
		if($isPartOf) {
			$form->input('idIsPartOf', $isPartOf,'hidden');
		}
		// headline
		$form->fieldsetWithInput('headline', $value['headline'] ?? null, _('Headline'));
		// alternativeHeadline
		$form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
		// text
		$form->fieldsetWithTextarea('text', $value['text'] ?? null, _('Text'));
		// author
		$form->relationshipOneToOne('person,organization',_('Author'), 'author', $value['author'] ?? null);
		// copyrightHolder
		//var_dump($value['copyrightHolder']); TODO: consertar
		$form->relationshipOneToOne('person,organization',_('Copyright holder'), 'copyrightHolder', $value['copyrightHolder'] ?? null);
		// keywords
		$form->fieldsetWithInput('keywords', $value['keywords'] ?? null, _('Keywords'));
		// position
		if ($position) $form->fieldsetWithInput('position', $position, _('Position'));
		// version
		$form->fieldsetWithInput('version', $value['version'] ?? null, _('Version'));
		// size
		$form->fieldsetWithInput('size', $value['size'] ?? null, _('Size'));
		// license
		$form->fieldsetWithInput('license', $value['license'] ?? null, _('License'));
		// acquireLicensePage
		$form->fieldsetWithInput('acquireLicensePage', $value['acquireLicensePage'] ?? null, _('Acquire license page'));

		// locationCreated
		//var_dump($value['locationCreated']); TODO: consertar
		$form->relationshipOneToOne('place',_('Location created'), 'locationCreated', $value['locationCreated'] ?? null);

		// editor
		//var_dump($editor); TODO: consertar
		$form->relationshipOneToOne('person',_('Editor'), 'editor', $editor);

		// publisher
		if ($publisher) $form->fieldsetWithInput('publisher', $publisher, _('Publisher'));

		// creative work status
		$form->fieldsetWithSelect('creativeWorkStatus', $creativeWorkStatus,[
			"draft"=>_("Draft"),
			"in production"=>_("In production"),
			"suspended"=>_("Suspended"),
			"Waiting for review"=>_("Waiting for review"),
			"published"=>_("Published")
		],_("Creative work status"), ['class'=>'form-creativeWork-creativeWorkStatus']);

		// datePublished
		if ($datePublished) {
			$form->fieldsetWithInput('datePublished', $datePublished, _('Date published'), 'datetime-local', null, ['disable']);
		}
		if ($type !== 'CreativeWork') {
			$form->content("</div>");
		}
		return $form;
	}
}
