<?php

namespace Plinct\Cms\Server;

class Server
{   
    private static $tableHasPart;
    
    public function edit($className, $params)
    {     
        (new $className())->put(self::unsetRelParams($params));
        
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($className, $params)
    {        
        (new $className())->post($params);
               
        return $this->return($params['tableIsPartOf'] ?? null);
    }
    
    public function delete($className, $params)    {   
        
        (new $className())->delete(self::unsetRelParams($params));
        
        return $this->return();
    }
    
    private static function unsetRelParams($params)
    {        
        self::$tableHasPart = $params['tableHasPart'];
        
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);        
        
        return $params;
    }
    
    private function return() 
    {        
        if (self::$tableHasPart) {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
            
        } else {                
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        }
    }
}
