<?php
namespace Plinct\Cms\View\WebSite\Type\Thing;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;

class ThingElements
{
	public static function form(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-thing", ['class'=>'form-basic form-thing']);
		$form->method('post')->action("/admin/thing/$case");
		$form = self::formThing($form, $value);
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

	/**
	 * @param Form $form
	 * @param array|null $value
	 * @param string|null $nameOfName
	 * @param array $excludes
	 * @return Form
	 */
	public static function formThing(Form $form, array $value = null, string $nameOfName = null, array $excludes = []): Form
	{
		$case = 'new';
		$idthing = null;
		$name = $value['name'] ?? null;
		$alternateName = $value['alternateName'] ?? null;
		$description = $value['description'] ?? null;
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$url = $value['url'] ?? null;
		if ($value) {
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
			$idthing = $typeBuilder->getPropertyValue('idthing') ?? null;
			$case = 'edit';
		}
		if (!$form->getIdform()) {
			$form->setIdform("form-name".($idthing ? "-$idthing" : '-new'));
		}
		$form->addMandatories('name');
		// CONTENT
		$form->content("<div class='form-thing-extract'>");
		// name
		$form->fieldsetWithInput('name', $name, $nameOfName ?? _('Name'));
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
			CmsFactory::view()->fragment()->box()->expandingBox(_('Description'),"<textarea name='description' class='thing-description' id='description$idthing' style='min-height: 300px;'>$description</textarea>", false,'width: 100%;')
		);
		$form->setEditor("description$idthing", "editor$case$idthing");
		// url
		$form->fieldsetWithInput('url', $url, _('url'));
		$form->content("</div>");
		//
		return $form;
	}
}
