<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Thing;

use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\Response\Fragment\Form\Form;

class ThingView
{
	/**
	 * @param string $tableHasPart
	 * @return array
	 */
	public static function new(string $tableHasPart): array
	{
		$form = new Form(['class'=>'form-basic form-thing']);
		$form->action('/admin/'.$tableHasPart.'/new')->method('post');
		// name
		$form->fieldsetWithInput('name',null,_('Name'));
		// description
		$form->fieldsetWithTextarea('description',null,_('Description'));
		// disambiguatingDescription
		$form->fieldsetWithTextarea('disambiguatingDescription', null, _('Disambiguating description'));
		// url
		$form->fieldsetWithInput('url',null,_('url'));
		// alternateName
		$form->fieldsetWithInput('alternateName', null, _('Alternate name'));
		// send
		$form->submitButtonSend();
		// ready
		return CmsFactory::response()->fragment()->box()->simpleBox($form->ready(), _('Add new') . ' ThingView.php' .$tableHasPart);
	}
}