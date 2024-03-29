<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\ImageObject;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\Server\Type\ImageObjectServer;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Image\Image;
use Plinct\Web\Element\Element;

class ImageObjectWidget
{
  /**
   * @var string
   */
  protected string $tableHasPart;
  /**
   * @var int
   */
  protected int $idHasPart;

	protected int $limit = 40;

	protected int $offset = 0;

	/**
	 * @return void
	 */
	protected function navBarLevel1()
	{
		View::contentHeader(Fragment::navbar()
			->title(_('Images'))
			->level(2)
			->newTab("/admin/imageObject", Fragment::icon()->home())
			->newTab("/admin/imageObject/new", Fragment::icon()->plus())
			->newTab("/admin/imageObject?listBy=keywords", _("Keywords"))
			->newTab("/admin/imageObject?listBy=groups", _("Groups"))
			->ready()
		);
	}

	protected function navBarLevel2($title)
	{
		self::navBarLevel1();
		View::contentHeader(Fragment::navbar()
			->title($title)
			->level(3)
			->ready()
		);
	}

  /**
   *
   * @param array $data
   * @return array
   * @throws Exception
   */
  protected function editWithPartOf(array $data): array
  {
    $content = null;

    if (empty($data)) {
      $content[] = [ "tag" => "p", "content" => _("Images not found!"), "attributes" => [ "class" => "warning"] ];

    } else {
      foreach ($data as $valueEdit) {
        $content[] = ['tag'=>'div', 'attributes'=>['class'=>'box', 'style'=>'overflow: hidden;'], "content"=> self::formIsPartOf($valueEdit)];
      }
    }

    return $content;
  }

  /**
   * @throws Exception
   */
  protected static function formImageObjectEdit($value): array
  {
    $ID = ArrayTool::searchByValue($value['identifier'], "id")['value'];
    // FIGURE
    $image = new Image($value['contentUrl']);
    $contentSize = $value['contentSize'] ?? (string) $image->getFileSize();
    $imageWidth = $value['width'] ?? (string) $image->getWidth();
    $imageHeight = $value['height'] ?? (string) $image->getHeight();
    $imageType = $value['type'] ?? $image->getEncodingFormat();

    $form = Fragment::form(["class" => "formPadrao form-imageObject", "name" => "form-imageObject", "enctype" => "multipart/form-data" ]);
    $form->action("/admin/imageObject/edit")->method("post");
    // figure
    $form->content(['object'=>'figure','src'=>$value['contentUrl']]);
    // id
    $form->input('id', $ID, 'hidden');
    $form->fieldsetWithInput("idimageObject", $ID, "Id", "text", null, ['disabled']);
    // url
    $form->fieldsetWithInput('contentUrl', $value['contentUrl'], "Url", 'text', null, ['readonly']);
    //content size
    $form->fieldsetWithInput('contentSize', $contentSize, _("Content size"), 'text', null, ['readonly']);
    // width
    $form->fieldsetWithInput("width", $imageWidth, _("Image width") . " (px)", 'text', null, ['readonly'] );
    // height
    $form->fieldsetWithInput('height', $imageHeight, _("Image height") . " (px)", "text", null, ["readonly"] );
    // encodingFormat
    $form->fieldsetWithInput("encodingFormat", $imageType,_("Encoding format"),  'text', null, ["disabled"] );
    // uploadDate
    $form->fieldsetWithInput("uploadDate", $value['uploadDate'],_("Upload date"),  "text", null, ["disabled"]);
    // license
    $form->fieldsetWithInput("license", $value['license'], _("License"));
    // group
    $form->fieldsetWithInput("keywords", $value['keywords'], _("Keywords")." [<a href='/admin/imageObject/keywords/".$value['keywords']."'>"._("edit")."</a>]");
    // submit buttons
    $form->submitButtonSend();
    $form->submitButtonDelete("/admin/imageObject/delete");
    // READY
    return $form->ready();
  }

	/**
   * @throws Exception
   */
	protected function formIsPartOf($value): array
	{
    $ID = $value['idimageObject'];

    $form = Fragment::form(["class" => "formPadrao form-imageObject-edit", "id" => "form-images-edit-$ID", "name" => "form-imageObject-edit", "enctype" => "multipart/form-data"]);
    $form->action("/admin/imageObject/edit")->method('post');
    // hiddens
    $form->input('tableHasPart', $this->tableHasPart, 'hidden');
    $form->input('idHasPart', (string) $this->idHasPart, 'hidden');
    $form->input('idIsPartOf', $ID, 'hidden');
    $form->input('tableIsPartOf', 'imageObject', 'hidden');
    $form->input('id', $ID, 'hidden');
    // image
    $image = new Image($value['contentUrl']);
    $caption = "Dimensions: " . $image->getWidth() . " x " .$image->getHeight() . " px<br>Size: " . $image->getFileSize() . " bytes";
    $form->content([
      "object" => "figure",
      "attributes"=>['class'=>'form-imageObject-edit-figure'],
      "src" => $image->getSrc(),
      "width" => 200,
      "href" => "/admin/imageObject/edit/$ID",
      "caption" => $caption
    ]);
    // content url
    $form->fieldsetWithInput("contentUrl", $value['contentUrl'], _("Content url"),  "text", null, [ "readonly" ]);
    // position
    $form->fieldsetWithInput("position", $value['position'] ?? '1', _("Position"), "number", null, [ "min" => "1" ]);
    // highlights
    $form->content([ "tag" => "fieldset", "content" => [
      [ "tag" => "legend", "content" => _("Representative of page") ],
      [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
        [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 1, ($value['representativeOfPage'] == 1 ? "checked" : null) ] ], _("Yes")
      ] ],
      [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
        [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 0, $value['representativeOfPage'] == 0 ? "checked" : null ] ], _("No")
      ] ]
    ]]);
    // image, height and href for use in web page element
    if (isset($value['width']) && $this->tableHasPart == "webPageElement") {
      // width
      $width = $value['width'] != '0.00' ? $value['width'] : null;
      $form->fieldsetWithInput('width', $width, _("Width"));
      // height
      $height = isset($value['height']) && $value['height'] != '0.00' ? $value['height'] : null;
      $form->fieldsetWithInput('height', $height, _('Height'));
      // href
      $form->fieldsetWithInput('href', $value['href'] ?? null, _("Link"));
    }
    // caption
    $form->fieldsetWithInput("caption", $value['caption'], _("Caption"));
    // submit buttons
    $form->submitButtonSend();
    $form->submitButtonDelete("/admin/imageObject/erase");
    // ready
    return $form->ready();
  }

  /**
   * @param $data
   * @return array
   */
  protected static function infoIsPartOf($data): array
  {
      $info = $data['info'];

      if ($info) {
          $list = null;
          foreach ($info as $value) {
              $href = "/admin/" . $value['tableHasPart'] . "/edit/" . $value['idHasPart'];
              $values = $value['values'];
              $name = $values['name'] ?? $values['headline'] ?? _("Undefined");
              $text = "<b>Type:</b> " . ucfirst($value['tableHasPart']) . (isset($values['url']) ? ". Url: " . $values['url'] : null);
              $list .= "<dd>$text - <b>Name:</b> <a href='$href'>$name</a></dd>";
          }
          $content[] = "<dl><dt>Is Part Of:</dt>$list</dl>";

      } else {
          $content[] = "<p style='color: yellow;'>". _("This item is not part of any other.") . "</p>";
      }

      return Fragment::box()->simpleBox($content);
  }

  /**
   * FORM UPLOAD IMAGES
   * @param null $tableHasPart
   * @param null $idHasPart
   * @return array
   */
  protected function upload($tableHasPart = null, $idHasPart = null): array
  {
      $form = Fragment::form(['class'=>'formPadrao form-imageObject-upload box','enctype'=>'multipart/form-data']);
      $form->action('/admin/imageObject/new')->method('post');
      // TITLE
      $form->content("<h4>"._("Upload images")."</h4>");
      // HIDDENS
      if ($tableHasPart && $idHasPart) {
          $form->input('tableHasPart',$tableHasPart,'hidden');
          $form->input('idHasPart',(string)$idHasPart,'hidden');
      }
      // IMAGE UPLOAD
      $form->fieldsetWithInput('imageupload[]', null, _("Select images"),'file',null,['multiple']);
      // LOCATION
      $form->content(self::locationsOnUpload());
      // KEYWORDS
      $form->content(self::keywordsOnUpload());
      // SUBMIT BUTTON
      $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
      // RESPONSE
      return $form->ready();
  }

  /**
   * @return array
   */
  private static function locationsOnUpload(): array
  {
      $imageDir = App::getImagesFolder();
      $datalist = ImageObjectServer::listLocation($imageDir, true);
      // FIELDSET
      $fieldset = new Element('fieldset');
      // legend
      $fieldset->content("<legend>"._("Save to folder")."</legend>");
      // label
      $fieldset->content("<label>$imageDir</label>");
      // input
      $fieldset->content("<input name='location' type='text' list='listlocations' autocomplete='off'/>");
      // data list
      $fieldset->content(Fragment::form()->datalist('listlocations',$datalist));
      // response
      return $fieldset->ready();
  }

  /**
   * @return array
   */
  private static function keywordsOnUpload(): array
  {
      $listKeywords = ImageObjectServer::listKeywords();
      // FIELDSET
      $fieldset = new Element('fieldset');
      // LEGEND
      $fieldset->content("<legend>"._("Keywords")."</legend>");
      // INPUT
      $fieldset->content('<input name="keywords" type="text" value="" list="keywords" autocomplete="off">');
      // DATA LIST
      $fieldset->content(Fragment::form()->datalist('keywords',$listKeywords));
      // RESPONSE
      return $fieldset->ready();
    }
}
