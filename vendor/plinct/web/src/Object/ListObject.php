<?php

namespace Plinct\Web\Object;

class ListObject 
{
    
    public function __invoke($array) 
    {
        return self::createList($array['content']);
    }
    
    static private function createList(array $array)
    {
        $content = null;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $content[] = self::createList($value);
                
            } else {
                $content[] = [ "tag" => "li", "content" => $value, "href" => is_string($key) ? $key : null ];
            }
        }       
        
        return [ "tag" => "ul", "attributes" => $value['attributes'] ?? null, "content" => $content ];
    }
}
