<?php
namespace Plinct\Cms\Server;

class Server {
    private $tableHasPart;

    public function edit($type, $params) {
        $classType = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";
        if (class_exists($classType)) {
            return (new $classType())->edit($params);
        }
        Api::put($type, $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($type, $params): string {
        $classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";
        if (class_exists($classTypeServer)) {
            return (new $classTypeServer())->new($params);
        }
        // API
        $data = Api::post($type, $params);
        if ($type == "product") {
            return self::httpReferrer();
        }
        // REDIRECT TO EDIT PAGE
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        $this->unsetRelParams($params);
        return $this->return();
    }
    
    public function erase($type, $params): string {
        // API ACTION
        $response = Api::delete($type, [ "id" => $params['id'] ]);
        // RESPONSE REDIRECT
        if (isset($response['message']) && $response['message'] == "Deleted successfully") {
            return isset($params['tableHasPart']) ? self::httpReferrer() : self::requestUri();
        } else {
            var_dump([ "error" => [ "response" => $response ]]);
            die;
        }
    }

    public function createSqlTable($type) {
        $classname = "Plinct\\Api\\Type\\".ucfirst($type);
        (new $classname())->createSqlTable($type);
    }

    private function unsetRelParams($params): void {
        $this->tableHasPart = $params['tableHasPart'] ?? null;
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);
    }

    private static function httpReferrer(): string {
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    private static function requestUri(): string {
        return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
    }

    private function return(): string {
        if ($this->tableHasPart) {
            return self::httpReferrer();
        } else {                
            return self::requestUri();
        }
    }
}
