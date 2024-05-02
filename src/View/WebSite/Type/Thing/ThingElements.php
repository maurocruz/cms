<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Thing;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Web\Element\Form\FormInterface;

class ThingElements
{
	public static function form(string $case = 'new', array $value = null): array
	{
		$typeBuilder = new TypeBuilder('thing',$value);
		$idthing = $typeBuilder->getId();
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-thing']);
		$form->method('post')->action("/admin/thing/$case");
		$form = self::formContent($form, $value);
		//button
		$form->submitButtonSend();
		if ($case === 'edit') {
			$form->input('idthing', (string) $idthing, 'hidden');
			$form->submitButtonDelete("/admin/thing/erase");
		}
		//return
		return $form->ready();
	}

	public static function formContent(FormInterface $form, array $value = null): FormInterface
	{
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		// name
		$form->fieldsetWithInput('name', $value['name'] ?? null, _('Name')." <span style='color: #eecc77;'>*</span>");
		// alternateName
		$form->fieldsetWithInput('alternateName', $value['alternateName'] ?? null, _('Alternate name'));
		// description
		$form->fieldsetWithTextarea('description', $value['description'] ?? null, _('Description'),['class'=>'thing-description']);
		// disambiguatingDescription
		$form->content(CmsFactory::view()->fragment()->box()->expandingBox(_('Disambiguating description'), "<textarea name='disambiguatingDescription' class='thing-disambiguatingDescription'>$disambiguatingDescription</textarea>", false, 'width: 100%;'));
		// url
		$form->fieldsetWithInput('url', $value['url'] ?? null, _('url'));
		//
		return $form;
	}
}