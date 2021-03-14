<?php

namespace Plinct\Web\Widget;

class Breadcrumb 
{
    private $response;
    
    public function __construct($attributes = null) 
    {
        $attrSchema = ["itemscope", "itemtype" => "http://schema.org/BreadcrumbList"];
        $attrol = $attributes ? array_merge($attrSchema, $attributes) : $attrSchema;              
        $this->response = [ 
            "tag"=>"ol", 
            "attributes" => $attrol,
            "content" => []
        ];
    }

    public function enableBreadcrumb($breadcrumb) 
    {
        if (isset($breadcrumb['numberOfItems']) && $breadcrumb['numberOfItems'] > 0 ? $breadcrumb['numberOfItems'] : $breadcrumb) {

            // json
            if (is_string($breadcrumb)) {
                $arrayBreadcrumb = json_decode($breadcrumb, true);        
                $this->response = $this->decodeArrayBreadcrumb($arrayBreadcrumb);
                
            } 
            
            // array
            elseif (is_array($breadcrumb)) {
                if (array_search("BreadcrumbList", $breadcrumb) == "@type") {
                    $this->decodeArrayBreadcrumb($breadcrumb);
                    
                } else {
                    $this->insertBreadcrumbBySimpleArray($breadcrumb);
                }            
            } 
            
            else {
                $this->addBreadcrumbFromURL();
            }
            
        } else {
            $this->response = $this->addBreadcrumbFromURL();
        }
        
        return $this->response;
    }

    public function addBreadcrumbFromURL($uri = null) 
    {        
        $paramsArray = explode("/", urldecode($uri ?? $_SERVER['REQUEST_URI']));         
        $i=0;
        $lastHref = "/";
        foreach ($paramsArray as $value) {
            // elimina querys estranhas
            if (strpos($value, "&") !== false) {
                $value = strstr($value, "&", true);
            }            
            $href = $lastHref.$value;            
            if ($value == '') {
                $value = "Inicial";
            } else {                
                $this->response['content'][] = [ "tag" => "li", "content" => " > " ]; 
            }            
            $text = strstr($value,"?",true) !== false ? strstr($value,"?",true) : $value;
            $this->response['content'][] = self::addli(ucfirst($text), $i, $href, count($paramsArray)==$i+1);
            $lastHref = $i > 0 ? $href."/" : $href;
            $i++;            
        }        
        return $this->response;
    }    
    
    private function decodeArrayBreadcrumb(array $arrayBreadcrumb) 
    {
        // inicial
        $this->response['content'][] = self::addli('Inicial',1,"/");     
        
        if (isset($arrayBreadcrumb['@context'])) {
            foreach ($arrayBreadcrumb['itemListElement'] as $key => $value) { 
                $this->response['content'][] = [ "tag" => "li", "content" => " > " ];
                $this->response['content'][] = self::addli($value["item"]['name'], $value['position'], $value['item']['@id'], $key+1==count($arrayBreadcrumb));
            }
            
        } else {
            foreach ($arrayBreadcrumb as $key => $value) {
                $this->response['content'][] = [ "tag" => "li", "content" => " > " ]; 
                $href = substr($value['item']['id'], -1) == '/' ? substr($value['item']['id'], 0, -1) : $value['item']['id'];
                $this->response['content'][] = self::addli($value["item"]['name'], $value['position'], $href, $key+1==count($arrayBreadcrumb));
            }
        }   
        
        return $this->response;
    }
    
    private function insertBreadcrumbBySimpleArray(array $arrayBreadcrumb) {
        // inicial
        $this->response['content'][] = self::addli('Inicial',1,"/");
        $i=1;
        foreach ($arrayBreadcrumb as $key => $value) {
            $this->response['content'][] = [ "tag" => "li", "content" => " > " ];     
            $this->response['content'][] = self::addli($value, $i, $key, $i==count($arrayBreadcrumb));
            $i++;
        }        
        return $this->response;
    }
    
    private static function addli($name, $position, $href, $thispage = false) 
    {    
        $attrSchemaLi = [ "itemprop" => "itemListElement", "itemscope", "itemtype" => "http://schema.org/ListItem" ];
        
        $attrSchemaA = [ "itemtype" => "http://schema.org/Thing", "itemprop" => "item", "href" => str_replace(" ", "+", $href) ];        
        
        $attrA = $thispage ? array_merge( ["class" => "breadcrumb-link-thispage"], $attrSchemaA) : array_merge(["class" => "breadcrumb-link"], $attrSchemaA);
        
        return [
            "tag" => "li", 
            "attributes" => $attrSchemaLi, 
            "content" => [
                [
                    "tag"=>"a", 
                    "attributes"=> $attrA,
                    "content" => [
                        ["tag"=>"span", "attributes" => ["itemprop" => "name"], "content" => $name ]
                    ]
                ],
                [
                    "tag" => "meta",
                    "attributes" => ["itemprop" => "position", "content" => $position]
                ]
            ] 
        ];
    }
}
