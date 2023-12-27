<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Book;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class BookView
{
	public function __construct()
	{
		CmsFactory::webSite()->navbar(_("Books"), [
			"/admin/book" => CmsFactory::response()->fragment()->icon()->home(),
			"/admin/book/new" => CmsFactory::response()->fragment()->icon()->plus()
		], 3, ["table"=>"book"]);
	}

	public function index()
	{
		CmsFactory::webSite()->addMain("<div id='plinctShell' data-type='Book' data-tablehaspart='book' data-apiHost='".App::getApiHost()."'></div>");
	}

	public function new()
	{
		CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox($this->form(), _("Add new")));
	}

	public function edit(array $value = null)
	{
		if ($value) {
			CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox($this->form('edit', $value), _("Edit")));
		} else {
			CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->noContent(_('No items found!')));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function form(string $case = 'new', array $value = null ): array
	{
		$form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-book'])->method('post');
		//action
		$form->action('/admin/book/'.$case);
		$form->content("<h4>"._("Book")."</h4>");
		// id
		if ($case == 'edit') {
			$form->input('idbook', $value['idbook'], 'hidden');
		}
		// name
		$form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
		// version
		$form->fieldsetWithInput('version', $value['version'] ?? null, _('Version'));
		// number of pages
		$form->fieldsetWithInput('numberOfPages', $value['numberOfPages'] ?? null, _('Number of pages'));
		// publisher
		$form->fieldsetWithInput('publisher', $value['publisher'] ?? null, _('Publisher'));
		// book edition
		$form->fieldsetWithInput('bookEdition', $value['bookEdition'] ?? null, _('Book edition'));
		// location created
		$form->fieldsetWithInput('locationCreated', $value['locationCreated'] ?? null, _('Location Created'));
		// date publisher
		$form->fieldsetWithInput('datePublished', $value['datePublished'] ?? null, _('Date published'));
		// keywords
		$form->fieldsetWithInput('keywords', $value['keywords'] ?? null, _('Keywords'));
		// author
		$form->fieldsetWithInput('author', $value['author'] ?? null, _('Author'));
		// birthdate
		$form->fieldsetWithInput('birthDate', $value['birthDate'] ?? null, _('Birth date'));
		// death date
		$form->fieldsetWithInput('deathDate', $value['deathDate'] ?? null, _('Death date'));
		// dateCreated
		if ($case == "edit") $form->fieldsetWithInput("dateCreated", $value['dateCreated'] ?? null, _("Date created"), "datetime", null, [ "disabled" ]);
		// dateModified
		if ($case == "edit") $form->fieldsetWithInput("dateModified", $value['dateModified'] ?? null, _("Date modified"), "datetime", null, [ "disabled" ]);
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/book/erase");
		}
		//return
		return $form->ready();
	}
}
