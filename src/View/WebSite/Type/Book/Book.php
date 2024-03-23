<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Book;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\CreativeWork;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Book implements TypeInterface
{
	private ?int $idbook;

	public function __construct()
	{
		CreativeWork::navbar();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('book')
				->setTitle(_('Book'))
				->newTab("/admin/book", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/book/new", CmsFactory::view()->fragment()->icon()->plus())

				->ready()
		);
	}

	public function index(?array $value)
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('book')->setColumnsTable(['name'=>_('Name'),'author'=>_('Author')])->ready()
		);
	}

	public function new(?array $value)
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->form(), _("Add new"))
		);
	}

	public function edit(array $data = null)
	{
		if (isset($data[0])) {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('book',$value);
			$this->idbook = $typeBuilder->getId();
			$idthing = $typeBuilder->getPropertyValue('idthing');
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox($this->form('edit', $data[0]), _("Edit"))
			);
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf($idthing)->ready()
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_('No items found!')));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function form(string $case = 'new', array $value = null ): array
	{
		$form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-book'])->method('post');
		//action
		$form->action('/admin/book/'.$case);
		$form->content("<h4>"._("Book")."</h4>");
		// id
		if ($case == 'edit') {
			$form->input('idbook', (string) $this->idbook, 'hidden');
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
