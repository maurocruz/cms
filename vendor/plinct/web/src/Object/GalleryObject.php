<?php

namespace Plinct\Web\Object;

class GalleryObject 
{
    public function __invoke($array): ?array
    {
        switch ($array['type'] ?? $array['gallery_type']) {
            case "grid":
                $attributes = [ "data-gallery" => "grid", "id" => "gallery-grid" ];
                $content[] = self::galleryGrid($array['figures']);
                
                return [ "tag" => "div", "attributes" => $attributes, "content" => $content ];

            case "carrousel":  
                $content[] = self::carrousel($array);                
                              
                $attributes = isset($array['attributes']) ? array_merge($array['attributes'],[ "name" => "gallery-carroussel" ]) : [ "name" => "gallery-carroussel" ];
                
                return [ "tag" => "div", "attributes" => $attributes, "content" => [
                    [ "tag" => "div", "attributes" => [ "data-gallery" => "carrousel", "id" => "gallery-carrousel" ], "content" => $content ],
                    [ "tag" => "script", "attributes" => [ "src" => "//pirenopolis.tur.br/App/static/js/galleryCarrousel.js", "type" => "text/javascript"] ]
                ]];

            default:
                return null;
        }
    }
    
    private static function galleryGrid($figures): array
    {
        $contentFigures = null;

        foreach ($figures as $valueFigures) {
            $contentFigures[] = [ "object" => "figure", "attributes" => [ "class" => "gallery-container--figure" ], "src" => $valueFigures['contentUrl'], "width" => 0.5 ];
        }

        return [ "tag" => "div", "attributes" => [ "class" => "gallery-container" ], "content" => $contentFigures ] ;
    }
    
    private static function carrousel($array): array
    {
        $figures = null;
        $thumbnails = null;

        // content
        foreach ($array['content'] ?? $array['figures'] as $valueFigures) {
            $figures[] = [ "object" => "figure", "attributes" => [ "class" => "gallery-container--figure" ], "src" => $valueFigures['contentUrl'], "width" => $valueFigures['width'] ?? 1, "height" => 0.75, "caption" => $valueFigures['caption'] ?? null ];
            $thumbnails[] = [ "object" => "image", "attributes" => [ "class" => "gallery-thumbnails--image" ], "src" => $valueFigures['contentUrl'], "width" => $array['thumbnail']['width'] ?? 120, "height" => 0.75 ];
        }
                
        
        return [            
            [ "tag" => "div", "attributes" => [ "class" => "gallery-container" ], "content" => $figures ],
            [ "tag" => "div", "attributes" => [ "class" => "gallery-thumbnails" ], "content" => [
                [ "tag" => "figure", "attributes" => [ "class" => "gallery-thumbnails--figure" ], "content" => $thumbnails ]
            ] ]
        ];
    }
}
