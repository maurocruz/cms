<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

class Server
{
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
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        }

        // REDIRECT TO EDIT PAGE
        if (isset($data['id']) && !isset($params['tableHasPart'])) {
            return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
        } else {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        }
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
        if (isset($params['id'])) {
            $params["id".lcfirst($type)] = $params['id'];
            unset($params['id']);
        }

        $response = Api::delete($type, $params);

        // RESPONSE REDIRECT
        if (isset($response['error'])) {
            print_r([ "error" => [ "response" => $response ]]);
            die("Error messge: {$response['error']['message']}");
        } else {
            return isset($params['tableHasPart']) ? filter_input(INPUT_SERVER, 'HTTP_REFERER') : dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
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
     * @param $type
     * @param $action
     * @param $params
     * @return string
     */
    public function request($type, $action, $params): string
    {
        Api::request($type,$action,$params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}
