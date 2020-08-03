<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Taxon;

class TaxonController implements ControllerInterface
{
    public function index($params = null): array 
    {        
        $params2 = [ "format" => "ItemList" ];
        
        $params3 = $params ? array_merge($params2, $params) : $params2;
        
        return (new Taxon())->get($params3);
    }
    
    public function edit(array $params): array 
    {        
        $params2 = array_merge($params,[ "properties" => "*,image" ]);
        return (new Taxon())->get($params2);
    }
    
    public function new() 
    {
        return true;
    }
}
