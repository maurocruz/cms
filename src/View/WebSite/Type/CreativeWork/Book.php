<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Book extends CreativeWorkView implements TypeViewInterface
{
	/**
	 * @var int|null
	 */
	private ?int $idbook;

	public function __construct(string $type = 'book', string $sitemapFilename = 'sitemap-book.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();
		//
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
			CmsFactory::view()->fragment()->box()->simpleBox($this->formCreativeWork(), _("Add new"))
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
			$typeBuilder = CmsFactory::helpers()->typeBuilder($value);
			$this->idbook = $typeBuilder->getId();
			$this->idthing = $typeBuilder->getIdthing();
			$this->name = $typeBuilder->getValue('name');
			// BOOK FORM
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox($this->formCreativeWork('edit', $data[0]), _("Edit"), $this->idthing, $this->name)
			);
			// IMAGE OBJECT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->ready()
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
	protected function formCreativeWork(string $case = 'new', array $value = null ): array
	{
		$bookFormat = $value['bookFormat'] ?? null;
		$illustrator = $value['illustrator'] ?? null;
		$isbn = $value['isbn'] ?? null;
		$bookEdition = $value['bookEdition'] ?? null;
		$numberOfPages = $value['numberOfPages'] ?? null;

		$form = CmsFactory::view()->fragment()->form("form-book",['class'=>'form-basic form-book']);
		$form->action('/admin/book/'.$case)->method('post');
		// id
		if ($case == 'edit') {
			$form->input('idbook', (string) $this->idbook, 'hidden');
		}
		// CREATIVE WORK FROM
		$form = parent::formCreativeWorkContent($form, $value);
		// illustrator
		$form->relationshipOneToOne('Person',_('Illustrator'),'illustrator',$illustrator);
		// book format
		$form->fieldsetWithSelect('bookFormat', $bookFormat,[
			"AudiobookFormat" => _("Audiobook"),
			"EBook" => _("Ebook"),
			"GraphicNovel" => _("Graphic novel"),
			"Hardcover" => _("Hardcover"),
			"Pamphlet" => _("Pamphlet"),
      "Paperback" => _("Paperback"),
		],_("Book format"), ['class'=>'form-book-bookFormat']);
		// isbn
		$form->fieldsetWithInput('isbn', $isbn, _('ISBN'));
		// book edition
		$form->fieldsetWithInput('bookEdition', $bookEdition, _('Book edition'));
		// number of pages
		$form->fieldsetWithInput('numberOfPages', $numberOfPages, _('Number of pages'));
		// buttons
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/book/erase");
		}
		//return
		return $form->ready();
	}
}
