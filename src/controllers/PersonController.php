<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Person;

class PersonController implements ControllerInterface
{
    public function index($params = null): array 
    {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ];
        
        $params3 = $params ? array_merge($params2, $params) : $params2;
        
        return (new Person())->get($params3);
    }
    
    public function new() 
    {
        return true;
    }
    
    public function edit(array $params): array 
    {
        $params = array_merge($params, [ "properties" => "contactPoint,address" ]);
        
        return (new Person())->get($params);
    }
    
    public function getContent() 
    {
        switch ($this->action) 
        {
            case 'edit':
                $get = new \fwc\Thing\PersonGet();
                $get->setProperties("givenName", "familyName", "additionalName", "taxId", "birthDate", "birthPlace", "gender", "hasOccupation", "contactPoint", "address");
                $get->setTypes("PostalAddress", "ContactPoint");
                $data = $get->selectById($this->identifier, null, "*", true); 
                //var_dump(json_decode($data));                
                return $data;
                
            default:
                $where = filter_input(INPUT_GET, 'search') ? "`name` LIKE '%$search%'" : null;
                $limit = 200;
                $order = "dateModified DESC, givenName ASC"; 
                
                return (new \fwc\Thing\PersonGet())->listAll($where, $order, $limit);
        }
    }
}
