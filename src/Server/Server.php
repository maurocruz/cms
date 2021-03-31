<?php
namespace Plinct\Cms\Server;

class Server extends Api {
    private $tableHasPart;

    public function edit($type, $params) {
        parent::put($type, $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($type, $params): string {
        // API
        $data = parent::post($type, $params);
        // REDIRECT TO EDIT PAGE
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        $this->unsetRelParams($params);
        return $this->return();
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
        return $params;
    }
    
    private function return(): string {
        if ($this->tableHasPart) {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        } else {                
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        }
    }
}
