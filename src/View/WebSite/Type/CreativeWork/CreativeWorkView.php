<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;

class CreativeWorkView extends ThingView
{
	/**
	 * @var string|null
	 */
	protected ?string $idcreativeWork = null;
	/**
	 * @var string|null
	 */
	protected ?string $idHasPart = null;
	/**
	 * @var string|null
	 */
	protected ?string $typeHasPart = null;
	/**
	 * @var string|null
	 */
	protected ?string $idIsPartOf = null;
	/**
	 * @var string|null
	 */
	protected ?string $typeIsPartOf = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'creativeWork', string $sitemapFilename = 'sitemap-creativeWork.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('creativeWork')
			->setTitle(_('Creative work'))
			->newTab('/admin/creativeWork',  CmsFactory::view()->fragment()->icon()->home())
			->newTab('/admin/creativeWork/new',  CmsFactory::view()->fragment()->icon()->plus())
			->setModulesAvailable(['Article','Book','Certification','Collection','MediaObject','Review','WebPage','WebPageElement','WebSite'])
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
			CmsFactory::view()->fragment()->reactShell('creativeWork')->ready()
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
			CmsFactory::view()->fragment()->box()->simpleBox(self::formCreativeWork())
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
			$typeBuilder = new TypeBuilder('creativeWork', $value);
			$this->idcreativeWork = $typeBuilder->getId();
			$this->idthing = $typeBuilder->getPropertyValue('idthing');
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formCreativeWork('edit', $value), _("Creative work")));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("No creative work were found!")));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function formCreativeWork(string $case = 'new', array $value = null): array
	{
		// FORM
		$form = CmsFactory::view()->fragment()->form("form-creativeWork", ['class'=>'form-basic form-creativeWork']);
		$form->method('post');
		$form->action("/admin/creativeWork/$case");
		// id
		if ($case == 'edit') {
			$form->input('idcreativeWork', (string) $this->idcreativeWork, 'hidden');
		}
		// creativeWork form
		self::formCreativeWorkContent($form, $value);
		//button
		$form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete("/admin/creativeWork/erase");
		}
		//return
		return $form->ready();
	}

	/**
	 * @param Form $form
	 * @param array|null $value
	 * @param mixed|null $mandatories
	 * @return Form
	 */
	public function formCreativeWorkContent(Form $form, array $value = null, array $mandatories = []): Form
	{
		$isPartOf = $this->idIsPartOf ?? null;
		$alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$position = isset($value['position']) ? (string) $value['position'] : null;
		$publisher = isset($value['publisher']) ? (string) $value['publisher'] : null;
		$editor = isset($value['editor']) ? (string) $value['editor'] : null;
		$datePublished = $value['datePublished'] ?? null;
		$creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		// thing
		$form = self::formThingContent($form, $value, $mandatories);
		if ($this->type !== 'CreativeWork') {
			$form->content(CmsFactory::view()->fragment()->box()->expandingBoxWithoutContent(_("Creative work"), "form-creativeWork", !$value));
		}
		// isPartOf
		if($isPartOf) {
			$form->input('idIsPartOf', $isPartOf,'hidden');
		}
		// headline
		$form->fieldsetWithInput('headline', $value['headline'] ?? null, _('Headline'));
		// alternativeHeadline
		$form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
		// text
		$form->fieldsetWithTextarea('text', $value['text'] ?? null, _('Text'));
		// author
		$form->relationshipOneToOne('person,organization',_('Author'), 'author', $value['author'] ?? null);
		// copyrightHolder
		//var_dump($value['copyrightHolder']); TODO: consertar
		$form->relationshipOneToOne('person,organization',_('Copyright holder'), 'copyrightHolder', $value['copyrightHolder'] ?? null);
		// keywords
		$form->fieldsetWithInput('keywords', $value['keywords'] ?? null, _('Keywords'));
		// position
		if ($position) $form->fieldsetWithInput('position', $position, _('Position'));
		// version
		$form->fieldsetWithInput('version', $value['version'] ?? null, _('Version'));
		// size
		$form->fieldsetWithInput('size', $value['size'] ?? null, _('Size'));
		// license
		$form->fieldsetWithInput('license', $value['license'] ?? null, _('License'));
		// acquireLicensePage
		$form->fieldsetWithInput('acquireLicensePage', $value['acquireLicensePage'] ?? null, _('Acquire license page'));

		// locationCreated
		//var_dump($value['locationCreated']); TODO: consertar
		$form->relationshipOneToOne('place',_('Location created'), 'locationCreated', $value['locationCreated'] ?? null);

		// editor
		//var_dump($editor); TODO: consertar
		$form->relationshipOneToOne('person',_('Editor'), 'editor', $editor);

		// publisher
		if ($publisher) $form->fieldsetWithInput('publisher', $publisher, _('Publisher'));

		// creative work status
		$form->fieldsetWithSelect('creativeWorkStatus', $creativeWorkStatus,[
			"draft"=>_("Draft"),
			"in production"=>_("In production"),
			"suspended"=>_("Suspended"),
			"Waiting for review"=>_("Waiting for review"),
			"published"=>_("Published")
		],_("Creative work status"), ['class'=>'form-creativeWork-creativeWorkStatus']);

		// datePublished
		if ($datePublished) {
			$form->fieldsetWithInput('datePublished', $datePublished, _('Date published'), 'datetime-local', null, ['disable']);
		}
		if ($this->type !== 'CreativeWork') {
			$form->content("</div>");
		}
		return $form;

	}
}