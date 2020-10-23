<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Place;

class PlaceController implements ControllerInterface
{
    public function index($params = null): array 
    {
        $params = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ], $params);
        
        return (new Place())->get($params);
    }
    
    public function new() 
    {
        return true;
    }
    
    public function edit(array $params): array 
    {
        $params= array_merge($params, [ "properties" => "address,image" ]);
        
        return (new Place())->get($params);
    }
}
