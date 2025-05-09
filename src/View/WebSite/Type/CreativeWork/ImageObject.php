<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\Image\Image;

class ImageObject implements TypeViewInterface
{
	/**
	 * @param string|null $title
	 * @return void
	 */
	protected function navBar(string $title = null): void
	{
		MediaObject::navbar();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Images'))
				->level(2)
				->newTab("/admin/imageObject", CmsFactory::view()->fragment()->icon()->home())
				->search()
				->ready()
		);
		if ($title) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title($title)
					->level(3)
					->ready()
			);
		}
	}


	public function index(?array $data, array $queryParams = null): void
	{
		self::navBar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('imageObject')->ready()
		);
	}

	public function new(?array $value, array $queryParams = null): bool
	{
		return false;
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @throws Exception
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		self::navBar();
		$value = $data[0];
		CmsFactory::view()->addMain(
			$this->formImageObjectEdit($value)
		);
	}

	/**
	 * @throws Exception
	 */
	protected function formImageObjectEdit($value): array
	{
		$tb = CmsFactory::toolBox()->typeBuilder($value);
		$idimageObject = $tb->getId();
		$dateModified = $tb->getPropertyValue('dateModified');
		// FIGURE
		//var_dump($value);
		$name = $value['name'];
		$contentUrl = $value['contentUrl'];
		$contentSize = $value['contentSize'] ?? null;
		$imageWidth = $value['width'] ?? null;
		$imageHeight = $value['height'] ?? null;
		$imageType = $value['encodingFormat'] ?? null;
		$mentions = $value['mentions'] ?? null;

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
		$form->content("<img src='$contentUrl' alt='$name'/>");
		// id
		$form->input('idimageObject', $idimageObject, 'hidden');
		$form->fieldsetWithInput("idimageObject", $idimageObject, "Id", "text", null, ['disabled']);
		// url
		$form->fieldsetWithInput('contentUrl', $value['contentUrl'], "Url", 'text', null, ['disabled']);
		//content size
		$form->fieldsetWithInput('contentSize', $contentSize, _("Content size"), 'text', null, ['disabled']);
		// width
		$form->fieldsetWithInput("width", $imageWidth, _("Image width") . " (px)", 'text', null, ['disabled'] );
		// height
		$form->fieldsetWithInput('height', $imageHeight, _("Image height") . " (px)", "text", null, ["disabled"] );
		// encodingFormat
		$form->fieldsetWithInput("encodingFormat", $imageType,_("Encoding format"),  'text', null, ["disabled"] );
		// license
		$form->fieldsetWithInput("license", $value['license'] ?? null, _("License"));
		// keywords
		$form->fieldsetWithInput("keywords", $value['keywords'], _("Keywords"));
		// author
		$form->relationshipOneToOne('person',_("Author"), 'author', $value['author'] ?? null);
		// uploadDate
		$form->fieldsetWithInput("uploadDate", $value['uploadDate'],_("Upload date"),  "datetime-local", null, ["disabled"]);
		// date modified
		$form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"), "datetime-local", null, ["disabled"]);
		// submit buttons
		$form->submitButtonSend();
		$form->submitButtonDelete("/admin/imageObject/delete");
		// mentions
		if ($mentions) {
			$mentionsElements = "<div class='form-imageObject-mentions box'><h4>"._('Mentions')."</h4>";
			foreach ($mentions as $mention) {
				$type = $mention['@type'];
				$name = $mention['name'];
				$tbMention = CmsFactory::toolBox()->typeBuilder($mention);
				$idMention = $tbMention->getId();
				$mentionsElements .= "<div class='form-imageObject-mentions-item'>";
				$mentionsElements .= "<p>Tipo: " . _($type) .". <a href='/admin/$type/edit/$idMention'>$name</a></p>";
				$mentionsElements .= "</div>";
			}
			$mentionsElements .= "</div>";
			$form->content($mentionsElements);
		}
		// READY
		return $form->ready();
	}

}