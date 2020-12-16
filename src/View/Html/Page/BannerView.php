<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Web\Widget\FormTrait;

class BannerView
{
    use FormTrait;
    
    public function getBannerByIdcontrato($data): array
    {
        $id = PropertyValue::extractValue($data['identifier'], "id");
        
        $content[] = [ "tag" => "h4", "content" => "Banner" ];

        if ($id) {
            $content[] = self::formBanner($data['idadvertising'], "edit", $data);
            // images
            $content[] = (new ImageObjectView())->getForm("banner", $id, $data['image']);
            
        } else {
            $content[] = self::formBanner($data['idadvertising']);
        } 
        
        return [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
    }
    
    private static function formBanner($idadvertising, $case = "add", $value = null): array
    {
        $content[] = $case == "edit" ? self::input("id", "hidden", PropertyValue::extractValue($value['identifier'],"id")) : null;

        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idadvertising", "type" => "hidden", "value" => $idadvertising ] ];

        // banner_title
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: calc(98% - 400px);" ], "content" => [
            [ "tag" => "legend", "content" => "Title" ],
            [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ] ]
        ] ];        
        // target
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 180px;" ], "content" => [
            [ "tag" => "legend", "content" => "target" ],
            [ "tag" => "input", "attributes" => [ "name" => "target", "type" => "text", "value" => $value['target'] ] ]
        ] ];        
        // position
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 180px;" ], "content" => [
            [ "tag" => "legend", "content" => "position" ],
            [ "tag" => "input", "attributes" => [ "name" => "position", "type" => "text", "value" => $value['position'] ] ]
        ] ];        
        // status
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 40px;" ], "content" => [
            [ "tag" => "legend", "content" => "status" ],
            [ "tag" => "input", "attributes" => [ "name" => "status", "type" => "text", "value" => $value['status'] ] ]
        ] ];        
        // banner_text
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%" ], "content" => [
            [ "tag" => "legend", "content" => "banner_text" ],
            [ "tag" => "textarea", "attributes" => [ "name" => "description", "style" => "min-height: 6rem;" ], "content" => $value['description'] ]
        ] ];        
        // banner_link
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 50%;" ], "content" => [
            [ "tag" => "legend", "content" => "banner_link" ],
            [ "tag" => "input", "attributes" => [ "name" => "url", "type" => "text", "value" => $value['url'] ] ]
        ] ];        
        // tags
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 50%;" ], "content" => [
            [ "tag" => "legend", "content" => "tags" ],
            [ "tag" => "input", "attributes" => [ "name" => "tags", "type" => "text", "value" => $value['tags'] ] ]
        ] ];    
        // style
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%" ], "content" => [
            [ "tag" => "legend", "content" => "style" ],
            [ "tag" => "input", "attributes" => [ "name" => "style", "type" => "text", "value" => $value['style'] ] ]
        ] ];  
        
        $content[] = self::submitButtonSend();

        $content[] = $case == "edit" ? self::submitButtonDelete("/admim/banner/erase") : null;

        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/banner/$case", "method" => "post" ], "content" => $content ];
    }
}
