<?php
namespace Plinct\Cms\View\Types\ImageObject;

use Plinct\Cms\App;
use Plinct\Cms\Server\ImageObjectServer;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Image;

class ImageObjectWidget {
    protected $tableHasPart;
    protected $idHasPart;

    use FormElementsTrait;

    protected function keywordsList($data): array {
        $list = null;
        $numberOfItems = $data['numberOfItems'];
        $content[] = [ "tag" => "p", "content" => sprintf(_("Listing %s groups"), $numberOfItems ) ];
        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            $list[] = [ "tag" => "li", "content" => [
                [ "object" => "figure",
                    "attributes" => [ "class" => "list-folder-figure" ],
                    "src" => $item['contentUrl'],
                    "caption" => $item['keywords'],
                    "width" => 110,
                    "height" => 0.68,
                    "href" => "/admin/imageObject/keywords/".urlencode($item['keywords'])
                ]
            ]];
        }
        $content[] = [ "tag" => "ul", "attributes" => [ "class" => "list-folder" ], "content" => $list ];
        return [ "tag" => "div", "content" => $content ];
    }

    protected function imagesList($data): array {
        $containerImages = null;
        $content[] = [ "tag" => "p", "content" => sprintf(_("Show %s items!"), $data['numberOfItems']) ];
        $imageServer = new ImageObjectServer();
        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            // is part of
            $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
            $info = $imageServer->getImageHasPartOf($id);
            $filename = $_SERVER['DOCUMENT_ROOT'].$item['contentUrl'];
            list($width, $height) = file_exists($filename) ? getimagesize($_SERVER['DOCUMENT_ROOT'].$item['contentUrl']) : null;
            $factor = 11;
            $span = $width ? ceil(($height/$width)*$factor)+3 : $factor+3;
            $n = $info ? "<b style='color: red'>".count($info)."</b>" : "<b style='color: green'>0</b>";
            $caption = "<p>".$width."x".$height."px. ".sprintf(_("Is part of %s items"), $n) . "</p>";
            $containerImages[] = [
                "object" => "figure",
                "attributes" => [ "class" => "admin-images-grid-figure", "style" => "grid-row-end: span $span" ],
                "src" => $item['contentUrl'],
                "width" => 200,
                "caption" => $caption,
                "href" => "/admin/imageObject/edit/$id",
                "imgAttributes" => [ "title" => $item['contentUrl'] ]
            ];
        }
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "admin-images-grid" ], "content" => $containerImages ];
        return [ "tag" => "div", "content" => $content ];
    }
    /**
     *
     * @param array $data
     * @return array
     */
    protected function editWithPartOf(array $data): array {
        $content = null;
        if (empty($data)) {
            $content[] = [ "tag" => "p", "content" => "Não há imagens!", "attributes" => [ "class" => "aviso"] ];
        } else {
            foreach ($data as $valueEdit) {
                $content[] = self::simpleTag("div", [
                    self::formIsPartOf($valueEdit)
                ], [ "class" => "box", "style" => "overflow: hidden;"]);
            }
        }
        return $content;
    }

    protected function formImageObject($value, $isPartOf = null, $info = null): array {
        $ID = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        if (isset($value['potentialAction'])) {
            foreach ($value['potentialAction'] as $valueAction) {
                $potentialAction[$valueAction['name']] = $valueAction['result'];
            }
        }
        // WEB PAGE
        $content[] = isset($isPartOf) && $isPartOf['@type'] == "WebPage" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $isPartOf['identifier'] ] ] : null;
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ] ];
        // FIGURE
        $content[] = [ "object" => "figure", "attributes" => [ "style" => "max-width: 200px; float: left; margin-right: 10px;" ], "src" => $value['contentUrl'], "width" => 200, "href" => "/admin/imageObject/edit/$ID" ];
        // ID
        $content[] = self::fieldsetWithInput(_("Id"), "idimageObject", $ID, [ "style" => "width: 60px;" ], "text", [ "readonly"] );
        $content[] = self::fieldsetWithInput(_("Url"), "contentUrl", $value['contentUrl'], [ "style" => "width: calc(100% - 300px);" ], "text", [ "readonly"] );
        // group
        $content[] = self::fieldsetWithInput(_("Keywords"), "keywords", $value['keywords'], [ "style" => "width: calc(100% - 315px);" ]);
        $content[] = self::submitButtonSend();
        $content[] = $info ? null  : self::submitButtonDelete("/admin/imageObject/delete");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    protected static function formImageObjectEdit($value): array {
        $ID = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $content[] = self::input("id", "hidden", $ID);
        // FIGURE
        $image = new Image($value['contentUrl']);
        $contentSize = $value['contentSize'] ?? $image->getFileSize();
        $imageWidth = $value['width'] ?? $image->getWidth();
        $imageHeight = $value['height'] ?? $image->getHeight();
        $imageType = $value['type'] ?? $image->getMimeType();
        $content[] = [ "object" => "figure", "attributes" => [ "style" => "display: block;" ], "src" => $value['contentUrl'] ];
        // ID
        $content[] = self::fieldsetWithInput(_("Id"), "idimageObject", $ID, [ "style" => "width: 80px;" ], "text", [ "disabled" ] );
        // url
        $content[] = self::fieldsetWithInput(_("Url"), "contentUrl", $value['contentUrl'], [ "style" => "width: calc(100% - 80px);" ], "text", [ "disabled"] );
        // content size
        $content[] = self::fieldsetWithInput(_("Content size") . " (bytes)", "contentSize", $contentSize, [ "style" => "width: 180px;" ], "text", [ "readonly"] );
        // width
        $content[] = self::fieldsetWithInput(_("Image width") . " (px)", "width", $imageWidth, [ "style" => "width: 160px;" ], "text", [ "readonly"] );
        // height
        $content[] = self::fieldsetWithInput(_("Image height") . " (px)", "height", $imageHeight, [ "style" => "width: 140px;" ], "text", [ "readonly"] );
        // encodingFormat
        $content[] = self::fieldsetWithInput(_("Encoding format"), "encodingFormat", $imageType, [ "style" => "width: 150px;" ], "text", [ "disabled"] );
        // uploadDate
        $content[] = self::fieldsetWithInput(_("Upload date"), "uploadDate", $value['uploadDate'], [ "style" => "width: 140px;" ], "text", [ "disabled"] );
        // license
        $content[] = self::fieldsetWithInput(_("License"), "license", $value['license'], [ "style" => "width: 100%;" ]);
        // group
        $content[] = self::fieldsetWithInput(_("Keywords")." [<a href='/admin/imageObject/keywords/".$value['keywords']."'>"._("edit")."</a>]", "keywords", $value['keywords'], [ "style" => "width: calc(100% - 315px);" ]);
        $content[] = self::submitButtonSend();
        $content[] = self::submitButtonDelete("/admin/imageObject/delete");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    protected function formIsPartOf($value): array {
        $ID = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idIsPartOf", "type" => "hidden", "value" => $ID ] ];
        // FIGURE
        $image = new Image($value['contentUrl']);
        $caption = "Dimensions: " . $image->getWidth() . " x " .$image->getHeight() . " px<br>Size: " . $image->getFileSize() . " bytes";
        $content[] = [
            "object" => "figure",
            "attributes" => [ "class" => "figure-caption-black", "style" => "max-width: 200px; float: left; margin-right: 10px;" ],
            "src" => $image->getSrc(),
            "width" => 200,
            "href" => "/admin/imageObject/edit/$ID",
            "caption" => $caption
        ];
        // content url
        $content[] = self::fieldsetWithInput(_("Content url"), "contentUrl", $value['contentUrl'], [ "style" => "width: 80%;" ], "text", [ "readonly" ]);
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'] ?? 1, [ "style" => "width: 80px;" ], "number", [ "min" => "1" ]);
        // highlights
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "min-width: 125px; margin: 5px 0;" ], "content" => [
            [ "tag" => "legend", "content" => _("Representative of page") ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 1, ($value['representativeOfPage'] == 1 ? "checked" : null) ] ], _("Yes")
            ] ],
            [ "tag" => "label", "attributes" => [ "class" => "labelradio" ], "content" => [
                [ "tag" => "input",  "attributes" => [ "name" => "representativeOfPage", "type" => "radio", "value" => 0, $value['representativeOfPage'] == 0 ? "checked" : null ] ], _("No")
            ] ]
        ]];
        // caption
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(100% - 435px); margin: 5px 0;" ], "content" => [
            [ "tag" => "legend", "content" => "Legenda" ],
            [ "tag" => "input", "attributes" => [ "name" => "caption", "type" => "text", "value" => $value['caption'] ?? null ] ]
        ]];
        if (isset($value['width'])) {
            // width
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 80px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Largura" ],
                [ "tag" => "input", "attributes" => [ "name" => "width", "type" => "text", "value" => $value['width'] ] ]
            ]];
            // height
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 80px; margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Altura" ],
                [ "tag" => "input", "attributes" => [ "name" => "height", "type" => "text", "value" => $value['height'] ] ]
            ]];
            // href
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(100% - 480px); margin: 5px 0;" ], "content" => [
                [ "tag" => "legend", "content" => "Link" ],
                [ "tag" => "input", "attributes" => [ "name" => "href", "type" => "text", "value" => $value['href'] ?? null ] ]
            ]];
        }
        //
        $content[] = self::submitButtonSend();
        $content[] = self::submitButtonDelete("/admin/imageObject/erase");
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "id" => "form-images-edit-{$ID}", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    protected static function infoIsPartOf($id, $info): array {
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
            $content[] = "<p style='color: yellow;'>". _("This item is not part of any other.") . "</p><button style='cursor: pointer;'>! "._("Delete the record without deleting the file")." !</button>";
        }
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao box", "style" => "overflow: hidden;", "name" => "form-images-edit", "action" => "/admin/imageObject/erase", "method" => "post" ], "content" => $content ];
    }

    protected function upload($tableHasPart = null, $idHasPart = null): array {
        $content[] = [ "tag" => "h4", "content" => "Enviar imagem" ];
        $content[] = $tableHasPart ? [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "value" => $tableHasPart, "type" => "hidden" ] ] : null;
        $content[] = $idHasPart ? [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "value" => $idHasPart, "type" => "hidden" ] ] : null;
        // image upload
        $content[] =self::fieldsetWithInput(_("Upload images"), "imageupload[]", null, null, "file", [ "multiple"]);
        // location
        $content[] = self::fieldsetWithInput(_("Save to folder"), "location", null, [ "style" => "width: 30%;" ], "text", [ "list" => "listLocations", "autocomplete" => "off"]);
        $datalist = ImageObjectServer::listLocation(App::getImagesFolder());
        $content[] = $datalist ? self::datalist("listLocations", $datalist) : null;
        // keywords
        $content[] = self::fieldsetWithInput(_("Keywords"), "keywords", null, [ "style" => "width: 10%;" ], "text", [ "list" => "keywords", "autocomplete" => "off" ] );
        $content[] = self::datalist("keywords", ImageObjectServer::listKeywords());
        $content[] = self::submitButtonSend();
        return [ "tag" => "form", "attributes" => [ "name" => "form-images-upload", "id" => "form-images-uploadImage-".$this->idHasPart, "action" => '/admin/imageObject/new', "class" => "box formPadrao", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }
}
