<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\Image\Image;

class ImageObjectView extends MediaObjectView implements TypeViewInterface
{
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
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formImageObjectEdit($value), _("Edit image")));
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
		$dateModified = $tb->getPropertyValue('dateModified');
		$name = $value['name'];
		$contentUrl = $value['contentUrl'];
		$contentSize = $value['contentSize'] ?? null;
		$imageWidth = $value['width'] ?? null;
		$imageHeight = $value['height'] ?? null;
		$imageType = $value['encodingFormat'] ?? null;
		$isPartOf = $value['isPartOf'] ?? null;

		if (!$contentSize || !$imageWidth || !$imageHeight || !$imageType) {
			$image = new Image($contentUrl);
			$contentSize = $value['contentSize'] ?? (string)$image->getFileSize();
			$imageWidth = $value['width'] ?? (string)$image->getWidth();
			$imageHeight = $value['height'] ?? (string)$image->getHeight();
			$imageType = $value['encodingFormat'] ?? $image->getEncodingFormat();
		}

		$form = CmsFactory::view()->fragment()->form("form-imageObject",["class" => "form-basic form-imageObject"]);
		$form->action("/admin/imageObject/edit")->method("post");
		// figure
		$form->content("<figure class='form-imageObject-image'><img src='$contentUrl' alt='$name'/></figure>");
		// id
		$form->input('idimageObject', $idimageObject, 'hidden');
		$form->fieldsetWithInput("idimageObject", $idimageObject, "Id", "text", null, ['disabled']);
		// url
		$form->fieldsetWithInput('contentUrl', $contentUrl, "Url", 'text', null, ['disabled']);
		//content size
		$form->fieldsetWithInput('contentSize', $contentSize, _("Content size"), 'text', null, ['disabled']);
		// width
		$form->fieldsetWithInput("width", $imageWidth, _("Image width") . " (px)", 'text', null, ['disabled'] );
		// height
		$form->fieldsetWithInput('height', $imageHeight, _("Image height") . " (px)", "text", null, ["disabled"] );
		// encodingFormat
		$form->fieldsetWithInput("encodingFormat", $imageType,_("Encoding format"),  'text', null, ["disabled"] );
		// author
		$form->relationshipOneToOne('person',_("Author"), 'author', $value['author'] ?? null);
		// license
		$form->fieldsetWithInput("license", $value['license'] ?? null, _("License"));
		// keywords
		$form->fieldsetWithInput("keywords", $value['keywords'] ?? null, _("Keywords"));
		// uploadDate
		$form->fieldsetWithInput("uploadDate", $value['uploadDate'],_("Upload date"),  "datetime-local", null, ["disabled"]);
		// date modified
		$form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"), "datetime-local", null, ["disabled"]);
		// submit buttons
		$form->submitButtonSend();
		$form->submitButtonDelete("/admin/imageObject/delete");
		// is part of
		if ($isPartOf) {
			$isPartOfElements = "<div class='form-imageObject-isPartOf box'><h4>"._('Is part of')."</h4>";
			foreach ($isPartOf as $mention) {
				$type = $mention['@type'];
				$name = $mention['name'];
				$tbIsPartOf = CmsFactory::toolBox()->typeBuilder($mention);
				$idIsPartOf = $tbIsPartOf->getId();
				$isPartOfElements .= "<div class='form-imageObject-isPartOf-item'>";
				$isPartOfElements .= "<p>Tipo: " . _($type) .". <a href='/admin/$type/edit/$idIsPartOf'>$name</a></p>";
				$isPartOfElements .= "</div>";
			}
			$isPartOfElements .= "</div>";
			$form->content($isPartOfElements);
		}
		// READY
		return $form->ready();
	}

}