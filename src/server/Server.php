<?php

namespace Plinct\Cms\Server;

class Server
{   
    private static $tableHasPart;
    
    public function edit($className, $params)
    {     
        (new $className())->put($params);
        
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($className, $params)
    {        
        $data = (new $className())->post($params);
        
        if (isset($data['id'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        
        return $this->return(self::unsetRelParams($params));
    }
    
    public function delete($className, $params)    
    {        
        (new $className())->delete($params);
        
        return $this->return(self::unsetRelParams($params));
    }
    
    private static function unsetRelParams($params)
    {        
        self::$tableHasPart = $params['tableHasPart'] ?? null;
        
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
