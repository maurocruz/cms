<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class VideoObjectView extends MediaObjectView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	protected ?string $idvideoObject = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'videoObject', string $sitemapFilename = 'sitemap-videoObject.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('videoObject')
				->title(_('Video'))
				->level(4)
				->newTab('/admin/videoObject', CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/videoObject/new', CmsFactory::view()->fragment()->icon()->plus())
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
			CmsFactory::view()->fragment()->reactShell('videoObject')->ready()
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
			CmsFactory::view()->fragment()->box()->simpleBox(self::formVideoObject(),_('Add new')." "._('video'))
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
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(self::formVideoObject('edit', $value), _('Edit') . " " . _('video'))
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No video found!'));
		}
	}

	protected function formVideoObject(string $case = 'new', ?array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form('form-videoObject',['class'=>'form-basic form-videoObject']);
		$form->action("/admin/videoObject/$case")->method('post');
		if ($value) {
			$form->content(CmsFactory::view()->fragment()->video($value['contentUrl'],['class'=>'form-videoObject-video']));
			$tbVideoObject = CmsFactory::toolBox()->typeBuilder($value);
			$idvideoObject = $tbVideoObject->getId();
			$form->input('idvideoObject',(string) $idvideoObject,'hidden');
		}
		// Form mediaObject
		$form = parent::formMediaObjectContent($form, $value);
		// submit
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/videoObject/erase');
		}
		return $form->ready();
	}
}
