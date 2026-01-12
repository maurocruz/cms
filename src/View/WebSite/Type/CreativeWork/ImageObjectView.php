<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\Image\Image;

class ImageObjectView extends MediaObjectView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	private ?string $idimageObject = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'imageObject', string $sitemapFilename = 'sitemap-imageObject.xml')
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
				->title(_('Images'))
				->level(2)
				->newTab("/admin/imageObject", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/imageObject/new", CmsFactory::view()->fragment()->icon()->plus())
				->search()
				->ready()
		);
	}


	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('imageObject')->ready()
		);
	}

	public function new(?array $data, array $queryParams = null): void
	{
		$form = CmsFactory::view()->fragment()->form("form-imageObject",["class" => "form-basic form-imageObject"]);
		$form->action("/admin/imageObject/new")->method("post");
		$form->fieldsetWithInput("imageupload[]", null, _("Image upload"), "file", null, ['multiple'=>'','accept'=>'image/*']);
		$form->fieldsetWithInput("location", null, _("Location"));
		$form->fieldsetWithInput("keywords", null, _("Keywords"));
		$form->submitButtonSend();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(
				$form->ready(),
				_("Add new")
			)
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @throws Exception
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			$tb = CmsFactory::toolBox()->typeBuilder($value);
			$this->idimageObject = $tb->getId();
			$this->name = $tb->getValue('name');
			$this->idthing = $tb->getIdthing();
			$this->idcreativeWork = $tb->getPropertyValue('idcreativeWork');
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox([
				self::formImageObjectEdit($value),
				CmsFactory::view()->fragment()->reactShell('imageObject')->setProperty('isPartOf')->setIdIsPartOf($this->idthing)->ready()
			], _("Edit image")));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('No image found!'));
		}
	}

	/**
	 * @throws Exception
	 */
	protected function formImageObjectEdit($value): array
	{
		$tb = CmsFactory::toolBox()->typeBuilder($value);
		$idimageObject = $tb->getId();
		$name = $value['name'];
		$contentUrl = $value['contentUrl'];
		$form = CmsFactory::view()->fragment()->form("form-imageObject",["class" => "form-basic form-imageObject"]);
		$form->action("/admin/imageObject/edit")->method("post");
		// figure
		$form->content("<figure class='form-imageObject-image'><img src='$contentUrl' alt='$name'/></figure>");
		// id hidden
		$form->input('idimageObject', $idimageObject, 'hidden');
		// media object content
		$form = parent::formMediaObjectContent($form, $value);
		// submit buttons
		$form->submitButtonSend();
		$form->submitButtonDelete("/admin/imageObject/delete");
		// READY
		return $form->ready();
	}

}