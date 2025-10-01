<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Fragment\CmsFragment;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class MediaObjectView extends CreativeWorkView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	private ?string $idmediaObject = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'mediaObject', string $sitemapFilename = 'sitemap-mediaObject.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('mediaObject')
			->title(_("Media Object"))
			->level(3)
			->newTab('/admin/mediaObject', CmsFactory::view()->fragment()->icon()->home())
			->newTab('/admin/mediaObject/new', CmsFactory::view()->fragment()->icon()->plus())
			->setModulesAvailable(['AudioObject','ImageObject','VideoObject'])
			->search()
			->ready()
		);
		if ($this->name) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->type('mediaObject')
					->title($this->name)
					->level(4)
					->newTab("/admin/mediaObject/edit/$this->idmediaObject", CmsFactory::view()->fragment()->icon()->home())
					->newTab("/admin/mediaObject/hasPart?idHasPart=$this->idthing", CmsFactory::view()->fragment()->icon()->attachment())
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
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('mediaObject')->ready());
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
			$tbMediaObject = CmsFactory::toolBox()->typeBuilder($value);
			$this->idmediaObject = $tbMediaObject->getId();
			$this->name = $tbMediaObject->getValue('name');
			$this->idthing = $tbMediaObject->getIdthing();
			CmsFactory::view()->addMain(
				CmsFragment::box()->simpleBox(self::formMediaObject('edit', $value), _('Edit') . " " . _('media object'))
			);
			parent::tableIsPartOf($value['isPartOf'] ?? null);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No media object found!'));
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFragment::box()->simpleBox(self::formMediaObject(),_('Add new')." "._('media object'))
		);
	}

	public function hasPart(?array $data): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			$tbMediaObject = CmsFactory::toolBox()->typeBuilder($value);
			$this->idmediaObject = $tbMediaObject->getId();
			$this->name = $tbMediaObject->getValue('name');
			$this->idthing = $tbMediaObject->getIdthing();
			$type = $tbMediaObject->getType();
			parent::hasPartContent($type,$this->idthing,$value['hasPart'] ?? null);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No media object found!'));
		}
	}


	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	protected function formMediaObject(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form('form-mediaObject',['class'=>'form-basic form-creativeWork form-mediaObject']);
		$form->action("/admin/mediaObject/$case")->method('post');
		if ($value) {
			$encodingFormat = $value['encodingFormat'] ?? null;
			$contentUrl = $value['contentUrl'] ?? null;
			if ($encodingFormat == 'application/pdf') {
				$form->content("<iframe src='$contentUrl' width='60%' height='600px' style='margin: 0 20%'></iframe>");
			}
			$tbMediaObject = CmsFactory::toolBox()->typeBuilder($value);
			$idmediaObject = $tbMediaObject->getId();
			$form->input('idmediaObject',(string) $idmediaObject,'hidden');
		}
		// form
		self::formMediaObjectContent($form, $value);
		// submit
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/mediaObject/erase');
		}
		return $form->ready();
	}

	/**
	 * @param Form $form
	 * @param array|null $value
	 * @return Form
	 */
	protected function formMediaObjectContent(Form $form, array $value = null): Form
	{
		// creativeWork
		$form = parent::formCreativeWorkContent($form, $value);
		if ($value) {
			if ($this->type !== 'MediaObject') {
				$form->content(CmsFragment::box()->expandigBoxWithoutContent(_("Media object") . " " . _('properties'), "form-mediaObject"));
			}
			// mediaObject properties
			$bitrate = $value['bitrate'] ?? null;
			$duration = $value['duration'] ?? null;
			$contentSize = $value['contentSize'] ?? null;
			$contentUrl = $value['contentUrl'] ?? null;
			$encodingFormat = $value['encodingFormat'] ?? null;
			$height = $value['height'] ?? null;
			$width = $value['width'] ?? null;
			$uploadDate = $value['uploadDate'] ?? null;
			// contentUrl
			$form->fieldsetWithInput('contentUrl', $contentUrl, _('Content url'), 'url', null, ['disabled']);
			// encoding format
			$form->fieldsetWithInput('encodingFormat', $encodingFormat, _('Encoding format'), 'text', null, ['disabled']);
			// width
			$form->fieldsetWithInput('width', $width, _('Width'), 'number', null, ['step' => '1','disabled']);
			// height
			$form->fieldsetWithInput('height', $height, _('Height'), 'number', null, ['step' => '1','disabled']);
			// duration
			$form->fieldsetWithInput('duration', $duration, _('Duration'), 'time', null, ['disabled']);
			// contentSize
			$form->fieldsetWithInput('contentSize', $contentSize, _('Content size'), 'number', null, ['disabled']);
			// bitrate
			$form->fieldsetWithInput('bitrate', $bitrate, _('Bitrate'), 'number', null, ['disabled']);
			// uploadDate
			$form->fieldsetWithInput('uploadDate', $uploadDate, _('Upload date'), 'datetime-local', null, ['disabled']);
			if ($this->type !== 'MediaObject') {
				$form->content("</div>");
			}
		}
		return $form;
	}
}