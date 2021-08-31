<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\ImageObject;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\Server\Type\ImageObjectServer;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Image\Image;
use Plinct\Web\Element\Element;
use Plinct\Web\Element\Form;

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

    use FormElementsTrait;

    /**
     * @param $data
     * @return array
     */
    protected function keywordsList($data): array
    {
        $list = null;
        $numberOfItems = $data['numberOfItems'];

        $content[] = [ "tag" => "p", "content" => sprintf(_("Listing %s groups"), $numberOfItems ) ];

        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            $keywords = $item['keywords'];
            $src = $item['contentUrl'];
            $href = "/admin/imageObject/keywords/".urlencode($keywords);
            $list[] = "<li class='imageObject-index' data-keywords='$keywords' data-contentUrl='$src'>
                <figure class='list-folder-figure' id='$keywords'>
                    <a href='$href'><img src='$src' alt='$keywords'></a>
                    <figcaption><a href='$href'>$keywords</a></figcaption>
                </figure>
            </li>";
        }

        $content[] = [ "tag" => "ul", "attributes" => ['id'=>'imageObject-listAll', "class" => "list-folder" ], "content" => $list ];

        return [ "tag" => "div", "content" => $content ];
    }

    /**
     * @param $data
     * @return array
     */
    protected function imagesList($data): array
    {
        $containerImages = null;
        $content[] = [ "tag" => "p", "content" => sprintf(_("Show %s items!"), $data['numberOfItems']) ];
        $imageServer = new ImageObjectServer();

        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            // vars
            $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
            $href = "/admin/imageObject/edit/$id";
            $src = $item['contentUrl'];

            // span;
            $filename = $_SERVER['DOCUMENT_ROOT'].$item['contentUrl'];
            list($width, $height) = file_exists($filename) ? getimagesize($_SERVER['DOCUMENT_ROOT'].$item['contentUrl']) : null;
            $factor = 11;
            $span = $width ? ceil(($height/$width)*$factor)+3 : $factor+3;

            // caption
            $info = $imageServer->getImageHasPartOf($id);
            $n = $info ? "<b style='color: red'>".count($info)."</b>" : "<b style='color: green'>0</b>";
            $caption = "<p>".$width."x".$height."px. ".sprintf(_("Is part of %s items"), $n) . "</p>";

            //element html
            $containerImages[] = "<figure class='admin-images-grid-figure' style='grid-row-end: span $span'>
                <a href='$href'><img src='$src' alt=''></a>
                <figcaption><a href='$href'>$caption</a></figcaption>
            </figure>";
        }

        $content[] = [ "tag" => "div", "attributes" => [ "class" => "admin-images-grid" ], "content" => $containerImages ];

        return [ "tag" => "div", "content" => $content ];
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
                $content[] = self::simpleTag("div", [
                    self::formIsPartOf($valueEdit)
                ], [ "class" => "box", "style" => "overflow: hidden;"]);
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
        $content[] = self::input("id", "hidden", $ID);
        // FIGURE
        $image = new Image($value['contentUrl']);
        $contentSize = $value['contentSize'] ?? $image->getFileSize();
        $imageWidth = $value['width'] ?? $image->getWidth();
        $imageHeight = $value['height'] ?? $image->getHeight();
        $imageType = $value['type'] ?? $image->getEncodingFormat();
        $content[] = [ "object" => "figure", "src" => $value['contentUrl'] ];
        // ID
        $content[] = self::fieldsetWithInput(_("Id"), "idimageObject", $ID, null, "text", [ "disabled" ] );
        // url
        $content[] = self::fieldsetWithInput(_("Url"), "contentUrl", $value['contentUrl'], null, "text", [ "readonly"] );
        // content size
        $content[] = self::fieldsetWithInput(_("Content size") . " (bytes)", "contentSize", $contentSize, null, "text", [ "readonly"] );
        // width
        $content[] = self::fieldsetWithInput(_("Image width") . " (px)", "width", $imageWidth, null, "text", [ "readonly"] );
        // height
        $content[] = self::fieldsetWithInput(_("Image height") . " (px)", "height", $imageHeight, null, "text", [ "readonly"] );
        // encodingFormat
        $content[] = self::fieldsetWithInput(_("Encoding format"), "encodingFormat", $imageType, null, "text", [ "disabled"] );
        // uploadDate
        $content[] = self::fieldsetWithInput(_("Upload date"), "uploadDate", $value['uploadDate'], null, "text", [ "disabled"] );
        // license
        $content[] = self::fieldsetWithInput(_("License"), "license", $value['license']);
        // group
        $content[] = self::fieldsetWithInput(_("Keywords")." [<a href='/admin/imageObject/keywords/".$value['keywords']."'>"._("edit")."</a>]", "keywords", $value['keywords']);
        $content[] = self::submitButtonSend();
        $content[] = self::submitButtonDelete("/admin/imageObject/delete");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-imageObject", "name" => "form-imageObject", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    /**
     * @throws Exception
     */
    protected function formIsPartOf($value): array
    {
        $ID = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idIsPartOf", "type" => "hidden", "value" => $ID ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableIsPartOf", "type" => "hidden", "value" => "imageObject" ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ] ];
        // FIGURE
        $image = new Image($value['contentUrl']);
        $caption = "Dimensions: " . $image->getWidth() . " x " .$image->getHeight() . " px<br>Size: " . $image->getFileSize() . " bytes";
        $content[] = [
            "object" => "figure",
            "attributes"=>['class'=>'form-imageObject-edit-figure'],
            "src" => $image->getSrc(),
            "width" => 200,
            "href" => "/admin/imageObject/edit/$ID",
            "caption" => $caption
        ];
        // content url
        $content[] = self::fieldsetWithInput(_("Content url"), "contentUrl", $value['contentUrl'], null, "text", [ "readonly" ]);
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'] ?? 1, null, "number", [ "min" => "1" ]);
        // highlights
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => _("Representative of page") ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 1, ($value['representativeOfPage'] == 1 ? "checked" : null) ] ], _("Yes")
            ] ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 0, $value['representativeOfPage'] == 0 ? "checked" : null ] ], _("No")
            ] ]
        ]];
        // image, height and href for use in web page element
        if (isset($value['width']) && $this->tableHasPart == "webPageElement") {
            // width
            $width = isset($value['width']) && $value['width'] != '0.00' ? $value['width'] : null;
            $content[] = [ "tag" => "fieldset", "content" => [
                [ "tag" => "legend", "content" => "Largura" ],
                [ "tag" => "input", "attributes" => [ "name" => "width", "type" => "text", "value" => $width ] ]
            ]];
            // height
            $height = isset($value['height']) && $value['height'] != '0.00' ? $value['height'] : null;
            $content[] = [ "tag" => "fieldset", "content" => [
                [ "tag" => "legend", "content" => "Altura" ],
                [ "tag" => "input", "attributes" => [ "name" => "height", "type" => "text", "value" => $height ] ]
            ]];
            // href
            $content[] = [ "tag" => "fieldset", "content" => [
                [ "tag" => "legend", "content" => "Link" ],
                [ "tag" => "input", "attributes" => [ "name" => "href", "type" => "text", "value" => $value['href'] ?? null ] ]
            ]];
        }
        // caption
        $content[] = self::fieldsetWithInput(_("Caption"), "caption", $value['caption']);
        //
        $content[] = self::submitButtonSend();
        $content[] = self::submitButtonDelete("/admin/imageObject/erase");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-imageObject-edit", "id" => "form-images-edit-$ID", "name" => "form-imageObject-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    /**
     * @param $data
     * @return array
     */
    protected static function infoIsPartOf($data): array
    {
        $id = ArrayTool::searchByValue($data['identifier'],"id","value");
        $contentUrl = $data['contentUrl'];
        $keywords = $data['keywords'];
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
            $content[] = self::input("id", "hidden", $id);
            $content[] = self::input("contentUrl", "hidden", $contentUrl);
            $content[] = self::input("keywords", "hidden", $keywords);
            $content[] = "<p style='color: yellow;'>". _("This item is not part of any other.") . "</p>";
            // button delete
            $content[] = sprintf(
                '<button style="cursor: pointer" onclick="return confirm(\'%s\');">%s</button>',
                _("Are you sure you want to delete this item?"),
                "! "._("Delete record and file")." !"
            );
        }
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao box", "style" => "overflow: hidden;", "name" => "form-images-edit", "action" => "/admin/imageObject/erase", "method" => "post" ], "content" => $content ];
    }

    /**
     * FORM UPLOAD IMAGES
     * @param null $tableHasPart
     * @param null $idHasPart
     * @return array
     */
    protected function upload($tableHasPart = null, $idHasPart = null): array
    {
        $form = new Form(['class'=>'formPadrao form-imageObject-upload box','enctype'=>'multipart/form-data']);
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
        $fieldset->content(self::datalist('listlocations',$datalist));
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
        $fieldset->content(self::datalist('keywords',$listKeywords));
        // RESPONSE
        return $fieldset->ready();
    }
}
