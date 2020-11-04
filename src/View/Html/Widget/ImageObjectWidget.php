<?php

namespace Plinct\Cms\View\Html\Widget;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\Controller\ImageObjectController;
use Plinct\Cms\Server\ImageObjectServer;
use Plinct\Web\Widget\FormTrait;

class ImageObjectWidget
{
    use FormTrait;

    protected function keywordsList($data)
    {
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
            ] ];
        }

        $content[] = [ "tag" => "ul", "attributes" => [ "class" => "list-folder" ], "content" => $list ];

        return [ "tag" => "div", "content" => $content ];
    }

    protected function imagesList($data)
    {
        $content[] = [ "tag" => "p", "content" => sprintf(_("Show %s items!"), $data['numberOfItems']) ];

        $imageServer = new ImageObjectServer();

        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            // is part of
            $idimageObject = PropertyValue::extractValue($item['identifier'], "id");
            $info = $imageServer->getImageHasPartOf($idimageObject);
            // from
            $content[] = self::simpleTag( "div", self::formImageObject($item, null, $info), [ "class" => "box", "style" => "overflow: hidden;" ]);
        }

        return [ "tag" => "div", "content" => $content ];
    }


    protected function formImageObject($value, $isPartOf = null, $info = null)
    {
        $ID = PropertyValue::extractValue($value['identifier'], "id");

        if (isset($value['potentialAction'])) {
            foreach ($value['potentialAction'] as $valueAction) {
                $potentialAction[$valueAction['name']] = $valueAction['result'];
            }
        }

        $content[] = isset($isPartOf) && $isPartOf['@type'] == "WebPage" ? [ "tag" => "input", "attributes" => [ "name" => "idwebPage", "type" => "hidden", "value" => $isPartOf['identifier'] ] ] : null;

        $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ] ];

        // figure
        $content[] = [ "object" => "figure", "attributes" => [ "style" => "max-width: 200px; float: left; margin-right: 10px;" ], "src" => $value['contentUrl'], "width" => 200 ];

        // ID
        $content[] = [ "tag" => "p", "content" => "[ID=$ID] ".$value['contentUrl'] ];

        // group
        $content[] = self::fieldsetWithInput(_("Keywords"), "keywords", $value['keywords'], [ "style" => "width: calc(100% - 315px);" ]);

        $content[] = self::submitButtonSend();

        $content[] = $info ? self::infoIsPartOf($info)  : self::submitButtonDelete("admin/imageObject/delete");

        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "style" => "overflow: hidden; display: inline;", "id" => "form-images-edit-{$ID}", "name" => "form-images-edit", "action" => "/admin/imageObject/edit", "enctype" => "multipart/form-data", "method" => "post" ], "content" => $content ];
    }

    private static function infoIsPartOf($info)
    {
        $list = null;

        foreach ($info as $value) {
            $href = "/admin/".$value['tableHasPart']."/edit/".$value['idHasPart'];
            $values = $value['values'];
            $list .= "<dd><a href='$href'>".ucfirst($value['tableHasPart'])." - ".($values['url'] ?? null)." - ".$values['name']."</a></dd>";
        }
        $content[] = "<dl><dt>Is Part Of:</dt>$list</dl>";

        return [ "tag" => "div", "attributes" => [ "class" => "box", "style" => "overflow: hidden;" ], "content" => $content ];
    }
}