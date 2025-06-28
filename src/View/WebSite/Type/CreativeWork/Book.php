<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Book extends CreativeWorkView implements TypeViewInterface
{
	/**
	 * @var int|null
	 */
	private ?int $idbook;

	/**
	 *
	 */
	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('book')
				->setTitle(_('Book'))
				->newTab("/admin/book", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/book/new", CmsFactory::view()->fragment()->icon()->plus())
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('book')->setColumnsTable(['name'=>_('Name'),'author'=>_('Author')])->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->form(), _("Add new"))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(array $data = null, array $queryParams = null): void
	{
		if (isset($data[0])) {

			$value = $data[0];
			$typeBuilder = new TypeBuilder('book',$value);
			$this->idbook = $typeBuilder->getId();
			$idthing = (int) $typeBuilder->getPropertyValue('idthing');
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox($this->form('edit', $data[0]), _("Edit"))
			);
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready()
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
	protected function form(string $case = 'new', array $value = null ): array
	{
		$author = $value['author'] ?? null;
		$version = $value['version'] ?? null;
		$numberOfPages = $value['numberOfPages'] ?? null;
		$publisher = $value['publisher'] ?? null;
		$bookEdition = $value['bookEdition'] ?? null;
		$locationCreated = $value['locationsCreated'] ?? null;
		$datePublished = $value['datePublished'] ?? null;
		$keywords = $value['keywords'] ?? null;

		$form = CmsFactory::view()->fragment()->form("form-book",['class'=>'form-basic form-book']);
		$form->action('/admin/book/'.$case)->method('post');
		// id
		if ($case == 'edit') {
			$form->input('idbook', (string) $this->idbook, 'hidden');
		}
		// THING
		$form = Thing::formContent($form, $value);
		// author
		$form->fieldsetWithInput('author', $author, _('Author'));
		// version
		$form->fieldsetWithInput('version', $version, _('Version'));
		// number of pages
		$form->fieldsetWithInput('numberOfPages', $numberOfPages, _('Number of pages'));
		// book edition
		$form->fieldsetWithInput('bookEdition', $bookEdition, _('Book edition'));
		// location created
		$form->relationshipOneToOne('place',_("Location created"), 'locationCreated', $locationCreated);
		// publisher
		$form->fieldsetWithInput('publisher', $publisher, _('Publisher'));
		// keywords
		$form->fieldsetWithInput('keywords', $keywords, _('Keywords'));
		// date publisher
		$form->fieldsetWithInput('datePublished', $datePublished, _('Date published'), "datetime-local");
		// dates
		if ($case == "edit") {
			$typeBuider = new TypeBuilder('book', $value);
			$dateCreated = $typeBuider->getPropertyValue('dateCreated');
			$dateModified = $typeBuider->getPropertyValue('dateModified');
			$form->fieldsetWithInput("dateCreated", $dateCreated, _("Date created"), "datetime-local", null, [ "disabled" ]);
			$form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"), "datetime-local", null, [ "disabled" ]);
		}
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/book/erase");
		}
		//return
		return $form->ready();
	}
}
