<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

class Server
{
    /**
     * @var string
     */
    private string $tableHasPart;

    /**
     * @param $type
     * @param $params
     * @return string
     */
    public function new($type, $params): string
    {
        $classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";

        if (class_exists($classTypeServer)) {
            $objectType = new $classTypeServer();
            if (method_exists($objectType,'new')) {
                return $objectType->new($params);
            }
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

    /**
     * @param $type
     * @param $params
     * @return mixed
     */
    public function edit($type, $params)
    {
        $classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";

        if (class_exists($classTypeServer)) {
            $objectType = new $classTypeServer();
            if (method_exists($objectType,"edit")) {
                return $objectType->edit($params);
            }
        }

        Api::put($type, $params);

        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    /**
     * @param $type
     * @param $params
     * @return string
     */
    public function erase($type, $params): string
    {
        $classTypeServer = __NAMESPACE__."\\Type\\".ucfirst($type)."Server";

        if (class_exists($classTypeServer)) {
            $objectType = new $classTypeServer();
            if (method_exists($objectType,"erase")) {
                return $objectType->erase($params);
            }
        }

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

    /**
     * @param $type
     */
    public function createSqlTable($type)
    {
        $classname = "Plinct\\Api\\Type\\".ucfirst($type);
        (new $classname())->createSqlTable($type);
    }

    /**
     * @param $params
     */
    private function unsetRelParams($params): void
    {
        $this->tableHasPart = $params['tableHasPart'] ?? null;
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);
    }

    /**
     * @return string
     */
    private static function httpReferrer(): string
    {
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    /**
     * @return string
     */
    private static function requestUri(): string
    {
        return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
    }

    /**
     * @return string
     */
    private function return(): string
    {
        if ($this->tableHasPart) {
            return self::httpReferrer();
        } else {                
            return self::requestUri();
        }
    }

    /**
     * @param $type
     * @param $action
     * @param $params
     * @return string
     */
    public function request($type, $action, $params): string
    {
        Api::request($type,$action,$params);
        return self::httpReferrer();
    }
}
