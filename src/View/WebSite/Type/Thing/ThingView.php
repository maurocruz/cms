<?php
namespace Plinct\Cms\View\WebSite\Type\Thing;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Helpers\CmsHelper;
use Plinct\Cms\View\Fragment\CmsFragment;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class ThingView implements TypeViewInterface
{
	/**
	 * @var ?string
	 */
	protected ?string $idthing = null;
	/**
	 * @var string
	 */
	protected string $type;
	/**
	 * @var ?string
	 */
	protected ?string $name = null;
	/**
	 * @var string
	 */
	protected string $sitemapFilename;
	/**
	 * @var string
	 */
	protected string $sitemapExtension = 'generic';

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'thing', string $sitemapFilename = 'sitemap.xml')
	{
		$this->type = $type;
		$this->sitemapFilename = $sitemapFilename;
	}

	/**
	 *
	 */
	public function __destruct() {
		if ($this->type == "thing") {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->type('thing')
					->title(_("Things"))
					->newTab("/admin/thing", CmsFactory::view()->fragment()->icon()->home())
					->newTab("/admin/thing/new", CmsFactory::view()->fragment()->icon()->plus())
					->newTab("/admin/thing/sitemap", CmsFactory::view()->fragment()->icon()->sitemap())
					->search()
					->ready()
			);
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('thing')->setColumnsTable(['@type'=>_('Types')])->ready()
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
			CmsFragment::box()->simpleBox(self::formThing(),_('New thing'))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
			$idname = "id$this->type";
			$this->$idname = $typeBuilder->getId();
			$this->idthing = $typeBuilder->getPropertyValue('idthing');
			$this->name = $typeBuilder->getValue('name');
			$formName = "form".ucfirst($this->type);
			CmsFactory::view()->addMain(CmsFragment::box()->simpleBox(self::$formName('edit', $value), _("Thing")));
			// images
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell($this->type)->setIdHasPart($this->idthing)->ready()
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("No $this->type were found!")));
		}
	}

	public function formThing(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-thing", ['class'=>'form-basic form-thing']);
		$form->method('post')->action("/admin/thing/$case");
		$form = self::formThingContent($form, $value);
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
	protected function formThingContent(Form $form, array $value = null, string $nameOfName = null, array $excludes = []): Form
	{
		$case = 'new';
		$idthing = null;
		$type = $value['@type'] ?? null;
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
			$form->setIdform("form-".($type ?? "type")."-".($idthing ?? 'new'));
		}
		$form->addMandatories('name');
		// CONTENT
		$form->content("<div class='form-thing-extract'>");
		$form->content("<p class='form-thing-extract-type'>$type</p>");
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
			CmsFragment::box()->expandingBox(_('Description'),"<textarea name='description' class='thing-description' id='description$idthing' style='min-height: 300px;'>$description</textarea>", false,'width: 100%;')
		);
		$form->setEditor("description$idthing", "editor$case$idthing");
		// url
		$form->fieldsetWithInput('url', $url, _('url'));
		// attachments
		if (!$value) {
			$accept = match ($this->type) {
				"audioObject" => "audio/*",
				"imageObject" => "image/*",
				"videoObject" => "video/*",
				default => null
			};
			$form->fieldsetWithInput('uploadfile[]', null, _('Attachment'), 'file', null, ['accept' => $accept]);
		}
		$form->content("</div>");
		//
		return $form;
	}

	public function hasPartContent(string $type, string $idthing, array $valueHasPart = null): void
	{
		CmsFactory::view()->addMain(
			CmsFragment::box()->simpleBox(self::formUploadfile(lcfirst($type),$idthing), _(CmsHelper::camelCaseToSentence($type))." "._('Has part'))
		);
		if ($valueHasPart) {
			$table = CmsFragment::table(['class'=>'table-hasPart']);
			$table->caption(_('Has part'));
			$table->labels(
				_('Edit'),
				'id',
				_('Type'),
				_('name'),
				_(CmsHelper::camelCaseToSentence('encodingFormat')),
				_('Date modified')
			);
			foreach ($valueHasPart as $hasPart) {
				$tbHasPart = CmsHelper::typeBuilder($hasPart);
				$idHasPart = $tbHasPart->getId();
				$type = $tbHasPart->getType();
				$dateModified = $tbHasPart->getPropertyValue('dateModified');
				$table->addRow(
					"<a href='/admin/$type/edit/$idHasPart'>".CmsFactory::view()->fragment()->icon()->edit(18,18)."</a>",
					$idHasPart,
					_(CmsHelper::camelCaseToSentence($tbHasPart->getType())),
					$hasPart['name'],
					$hasPart['encodingFormat'],
					CmsHelper::dateTime($dateModified)->readyDateTimeWithLiteral(),
				);
			}
			CmsFactory::view()->addMain($table->ready());
		} else {
			CmsFactory::view()->addMain(CmsFragment::noContent('No has part found!'));
		}
	}

	public function tableIsPartOf(array $valueIsPartOf = null): void
	{
		if ($valueIsPartOf) {
			$table = CmsFragment::table(['class'=>'table-isPartOf']);
			$table->caption(_('Is part of'));
			$table->labels(
				_('Edit'),
				_('Id'),
				_('Name'),
				_(CmsHelper::camelCaseToSentence('encodingFormat')),
				_('Date modified')
			);
			foreach ($valueIsPartOf as $isPartOf) {
				$tbIsPartOf = CmsFactory::toolBox()->typeBuilder($isPartOf);
				$typeIsPartOf = $tbIsPartOf->getType();
				$idIsPartOf = $tbIsPartOf->getId();
				$name = $tbIsPartOf->getValue('name');
				$encodingFormat = $tbIsPartOf->getValue('encodingFormat');
				$dateModified = $tbIsPartOf->getPropertyValue('dateModified');
				$table->addRow(
					"<a href='/admin/$typeIsPartOf/edit/$idIsPartOf'>".CmsFactory::view()->fragment()->icon()->edit(18,18)."</a>",
					$idIsPartOf,
					$name,
					$encodingFormat,
					CmsHelper::dateTime($dateModified)->readyDateTimeWithLiteral(),
				);
			}
			CmsFactory::view()->addMain($table->ready());
		} else {
			CmsFactory::view()->addMain("<p>"._('This item is not part of any other!')."</p>");
		}
	}
	/**
	 * @param string $typeHasPart
	 * @param string $idHasPart
	 * @return array
	 */
	protected function formUploadfile(string $typeHasPart, string $idHasPart): array
	{
		$form = CmsFragment::form("form-uploadfile", ['class'=>'form-basic form-uploadfile']);
		$form->method('post')->action("/admin/thing/post");
		$form->input('typeHasPart', $typeHasPart, 'hidden');
		$form->input('idHasPart', $idHasPart, 'hidden');
		$form->fieldsetWithInput('uploadfile[]', null, _('Has part'), 'file');
		$form->submitButtonSend();
		return $form->ready();
	}

	/**
	 * @param array $data
	 * @param array|null $queryParams
	 * @return void
	 * @throws DOMException
	 */
	public function sitemap(array $data, array $queryParams = null): void
	{
		$sitemap = CmsFactory::helpers()->sitemap($this->type);
		$sitemap->setFilename($this->sitemapFilename);
		$sitemap->setNamespace($this->sitemapExtension);
		$sitemap->setDataSitemap($data);
		$result = $sitemap->saveSitemap();
		CmsFactory::view()->fragment()->sitemapReturn($result, $sitemap->getFilename());
	}

}
