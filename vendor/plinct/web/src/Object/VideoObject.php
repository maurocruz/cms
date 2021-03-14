<?php

namespace Plinct\Web\Object;

class VideoObject 
{
    public function __invoke($array): array
    {
        $array['attributes']['src'] = $array['src'];
        return [ "tag" => "video", "attributes" => $array['attributes'] ];
    }
}
