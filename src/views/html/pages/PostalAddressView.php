<?php

namespace Plinct\Cms\View\Html\Page;

class PostalAddressView
{    
    use \Plinct\Web\Widget\FormTrait;
    
    public function getForm($tableHasPart, $idHasPart, $value) 
    {
        return self::form($tableHasPart, $idHasPart, $value ? 'edit' : 'new', $value);
    }
    
    static private function form($tableHasPart, $idHasPart, $case = 'new', $value = null) 
    {
        $content[] = self::input('tableHasPart', "hidden", $tableHasPart);
        $content[] = self::input('idHasPart', "hidden", $idHasPart);
        $content[] = self::input('tableIsPartOf', "hidden", "postalAddress");
        
        if ($case == "edit") {
            $id = \Plinct\Api\Type\PropertyValue::extractValue($value['identifier'], 'id');
            $content[] = self::input("id", "hidden", $id);
        }
        
        // streetAddress
        //$content[] = self::fieldsetWithInput(_("Street address"), "streetAddress", $value['streetAddress'], [ "style" => "width: calc(100% - 650px)"], "text", [ "data-type" => "PostalAddress", "data-property" => "streetAddress", "onKeyUp" => "searchAndFill(event);", "autocomplete" => "off" ]);
        $content[] = self::fieldsetWithInput(_("Street address"), "streetAddress", $value['streetAddress'], [ "style" => "width: calc(100% - 650px)"]);
        
        // addressLocality
        $content[] = self::fieldsetWithInput(_("Address locality"), "addressLocality", $value['addressLocality'], [ "style" => "width: 200px;"]);
        
        // addressRegion
        $content[] = self::fieldsetWithInput(_("Address region"), "addressRegion", $value['addressRegion'], [ "style" => "width: 120px;"]);
        
        // addressCountry
        $content[] = self::fieldsetWithInput(_("Address country"), "addressCountry", $value['addressCountry'], [ "style" => "width: 120px;"]);
        
        // postalCode
        $content[] = self::fieldsetWithInput(_("Postal code"), "postalCode", $value['postalCode'], [ "style" => "width: 100px;"]);
        
        // submits
        $content[] = self::submitButtonSend();
        
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/postalAddress/erase") : null;
        
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/postalAddress/$case", "method" => "post" ], "content" => $content ];
    }
}
