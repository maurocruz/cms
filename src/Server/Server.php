<?php
namespace Plinct\Cms\Server;

class Server extends Api {
    private $tableHasPart;

    public function new($type, $params): string {
        $classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";
        if (class_exists($classTypeServer)) {
            return (new $classTypeServer())->new($params);
        }
        // API
        $data = parent::post($type, $params);
        // REDIRECT TO EDIT PAGE
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        $this->unsetRelParams($params);
        return $this->return();
    }

    public function edit($type, $params) {
        $className = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";
        if (class_exists($className)) {
            $object = new $className();
            if (method_exists($object,"edit")) {
                return $object->edit($params);
            }
        }
        Api::put($type, $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
    
    public function erase($type, $params): string {
        parent::delete($type, $params);
        $this->unsetRelParams($params);
        return $this->return();
    }

    public function createSqlTable($type) {
        $classname = "Plinct\\Api\\Type\\".ucfirst($type);
        (new $classname())->createSqlTable($type);
    }

    private function unsetRelParams($params) {
        $this->tableHasPart = $params['tableHasPart'] ?? null;
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);
    }
    
    private function return(): string {
        if ($this->tableHasPart) {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        } else {                
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        }
    }
}
