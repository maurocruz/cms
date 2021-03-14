<?php

namespace Plinct\Web\Object;

use Plinct\Tool\Thumbnail;

class ImageObject {
    
    public function __invoke($value): array
    {
        $caption = isset($value['caption']) ? strip_tags($value['caption']) : null;
        $alt = $value['alt'] ?? $caption ?? "Image: ".basename($value['src']);

        $originalSrc = [ "data-source" => $value['src'], "data-caption" => $caption ?? null, "alt" => $alt ];
        
        $attributes = isset($value['attributes']) ? array_merge($originalSrc, $value['attributes']) : $originalSrc;        
        
        if(isset($value['width']) && $value['width'] !== '0') {            
            $srcAttributes = (new Thumbnail($value['src']))->getThumbnailAsAttributesImg($value);            
            
        } else {
            $srcAttributes = [ "src" => $value['src'] ];
            
        }   
        
        $imgTag = [ "tag" => "img", "attributes" => array_merge($srcAttributes, $attributes) ];        
        
        if (isset($value['href']) && $value['href'] !== '') {
            $hrefAttr = isset($value['hrefAttributes']) ? array_merge([ "href" => $value['href'] ], $value['hrefAttributes']) : [ "href" => $value['href'] ];
            
            $imgTag = [ "tag" => "a", "attributes" => $hrefAttr, "content" => $imgTag ];
        }   
        
        return $imgTag;
    }
}
