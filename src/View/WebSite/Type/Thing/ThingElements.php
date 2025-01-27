<?php
namespace Plinct\Cms\View\WebSite\Type\Thing;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;

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
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
			$idthing = $typeBuilder->getId();
			$form->input('idthing', (string) $idthing, 'hidden');
			$form->submitButtonDelete("/admin/thing/erase");
		}
		//return
		return $form->ready();
	}

	public static function formContent(Form $form, array $value = null, array $excludes = []): Form
	{
		$case = 'new';
		$idthing = null;
		$name = $value['name'] ?? null;
		$alternateName = $value['alternateName'] ?? null;
		$description = $value['description'] ?? null;
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$url = $value['url'] ?? null;
		$form->addMandatories('name');
		if ($value) {
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
			$idthing = $typeBuilder->getPropertyValue('idthing') ?? null;
			$case = 'edit';
		}
		$form->content("<div class='form-thing-extract'>");
		// name
		$form->fieldsetWithInput('name', $name, _('Name'));
		// alternateName
		if (!in_array('alternateName', $excludes)) {
			$form->fieldsetWithInput('alternateName', $alternateName, _('Alternate name'));
		}
		// disambiguatingDescription
		if (!in_array('disambiguatingDescription', $excludes)) {
			$form->fieldsetWithTextarea('disambiguatingDescription', $disambiguatingDescription, _('Short description for disambiguating'),['class'=>'thing-disambiguatingDescription']);
		}
		// description
		$form->content(
			CmsFactory::view()->fragment()->box()->expandingBox(_('Description'),"<textarea name='description' class='thing-description' id='description$idthing'>$description</textarea>", false,'width: 100%;')
		);
		$form->setEditor("description$idthing", "editor$case$idthing");
		// url
		$form->fieldsetWithInput('url', $url, _('url'));
		$form->content("</div>");
		//
		return $form;
	}
}
