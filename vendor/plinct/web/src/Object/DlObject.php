<?php

namespace Plinct\Web\Object;

/**
 * DlObject
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class DlObject
{
    public function __invoke($array) 
    {
        foreach ($array['content'] as $value) 
        {
            $content[] = [ "tag" => $value['type'] ?? 'dd', "content" => $value['content'] ];
        }
        
        return [ "tag" => "dl", "attributes" => $array['attributes'] ?? null, "content" => $content ];
    }
}
