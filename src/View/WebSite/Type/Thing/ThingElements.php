<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Thing;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;

class ThingElements
{
	public static function form(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-thing']);
		$form->method('post')->action("/admin/thing/$case");
		$form = self::formContent($form, $value);
		//button
		$form->submitButtonSend();
		if ($case === 'edit') {
			$typeBuilder = new TypeBuilder('thing',$value);
			$idthing = $typeBuilder->getId();
			$form->input('idthing', (string) $idthing, 'hidden');
			$form->submitButtonDelete("/admin/thing/erase");
		}
		//return
		return $form->ready();
	}

	public static function formContent(Form $form, array $value = null, array $excludes = null): Form
	{
		$case = 'new';
		$idthing = null;
		if ($value) {
			$typeBuilder = new \Plinct\Tool\TypeBuilder($value);
			$idthing = $typeBuilder->getPropertyValue('idthing') ?? null;
			$case = 'edit';
		}
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		// name
		$form->fieldsetWithInput('name', $value['name'] ?? null, _('Name')." <span style='color: #eecc77;'>*</span>");
		if (!in_array('alternateName', $excludes)) {
			// alternateName
			$form->fieldsetWithInput('alternateName', $value['alternateName'] ?? null, _('Alternate name'));
		}
		// description
		$form->fieldsetWithTextarea('description', $value['description'] ?? null, _('Description'),['class'=>'thing-description']);
		if (!in_array('disambiguatingDescription', $excludes)) {
			// disambiguatingDescription
			$form->content(CmsFactory::view()->fragment()->box()->expandingBox(_('Disambiguating description'), "<textarea name='disambiguatingDescription' class='thing-disambiguatingDescription' id='disambiguatingDescription$idthing'>$disambiguatingDescription</textarea>", false, 'width: 100%;'));
			$form->setEditor("disambiguatingDescription$idthing", "editor$case$idthing");
		}
		// url
		$form->fieldsetWithInput('url', $value['url'] ?? null, _('url'));
		//
		return $form;
	}
}