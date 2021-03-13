<?php

namespace Plinct\Web\Object;

class NavbarObject 
{
    public function __invoke($value) 
    {
        // title
        $content[] = isset($value['title']) ? [ "tag" => "h1", "content" => $value['title'] ] : null;
        
        foreach ($value['content'] as $key => $valueContent) {
            $content[] = [ "tag" => "a", "attributes" => [ "href" => $key ], "content" => $valueContent ];
        }

        $content[] = $value['append'] ?? null;

        return [ "tag" => "nav", "attributes" => $value['attributes'] ?? null, "content" => $content ];
    }    
}
