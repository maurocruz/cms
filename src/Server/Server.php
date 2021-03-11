<?php
namespace Plinct\Cms\Server;

class Server {
    private static $tableHasPart;
    
    public function edit($className, $params) {
        (new $className())->put($params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($className, $params): string {
        $data = (new $className())->post($params);
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        self::unsetRelParams($params);
        return $this->return();
    }
    
    public function delete($className, $params): string {
        (new $className())->delete($params);
        self::unsetRelParams($params);
        return $this->return();
    }

    private static function unsetRelParams($params) {
        self::$tableHasPart = $params['tableHasPart'] ?? null;
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);
        return $params;
    }
    
    private function return(): string {
        if (self::$tableHasPart) {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        } else {                
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        }
    }
}
