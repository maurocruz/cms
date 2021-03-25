<?php
namespace Plinct\Cms\Server;

use Plinct\Cms\App;
use Plinct\Tool\Curl;

class Server {
    private ?string $tableHasPart;
    
    public function edit($type, $params) {
        (new Curl(App::$API_HOST))->put($type, $params, $_COOKIE['API_TOKEN']);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public function new($type, $params): string {
        $data = json_decode((new Curl(App::$API_HOST))->post($type, $params, $_COOKIE['API_TOKEN']), true);
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        }
        $this->unsetRelParams($params);
        return $this->return();
    }
    
    public function delete($type, $params): string {
        $this->request($type, "delete", $params);
        $this->unsetRelParams($params);
        return $this->return();
    }

    public function request($type, $action, $params) {
        $token = filter_input(INPUT_COOKIE, "API_TOKEN");
        return (new Curl(App::$API_HOST))->{$action}($type, $params, $token);
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
