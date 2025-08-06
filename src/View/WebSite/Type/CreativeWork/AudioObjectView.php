<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class AudioObjectView extends MediaObjectView implements TypeViewInterface
{
	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'audioObject', string $sitemapFilename = 'sitemap-audioObject.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('audioObject')
				->title(_('Audio'))
				->level(4)
				->newTab('/admin/audioObject', CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/audioObject/new', CmsFactory::view()->fragment()->icon()->plus())
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
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('audioObject')->ready());
	}

	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formAudioObject(),_('Add new')." "._('audio'))
		);
	}

	public function edit(?array $data, array $queryParams = null): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formAudioObject('edit', $value), _('Edit') . " " . _('audio'))
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No audio found!'));
		}
	}

	protected function formAudioObject(string $case = 'new', ?array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form('form-audioObject',['class'=>'form-basic form-audioObject']);
		$form->action("/admin/audioObject/$case")->method('post');
		if ($value) {
			$form->content(CmsFactory::view()->fragment()->audio($value['contentUrl'],['class'=>'form-audioObject-audio']));
			$tbAudioObject = CmsFactory::toolBox()->typeBuilder($value);
			$idaudioObject = $tbAudioObject->getId();
			$form->input('idaudioObject',(string) $idaudioObject,'hidden');
		}
		// form mediaObject
		$form = parent::formMediaObjectContent($form, $value);
		// submit
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/audioObject/erase');
		}
		return $form->ready();
	}


}
