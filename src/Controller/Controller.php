<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

class Controller
{
    /**
     * @var string
     */
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param $type
     * @param $methodName
     * @param $id
     * @param $params
     * @return mixed
     */
    public function getData($type, $methodName, $id, $params)
    {
        unset($params['type']);

        $methodName = $methodName == 'index' && isset($id) ? 'edit' : $methodName;

        if($id) $params['id'] = $id;

        $controlClassName = "\\Plinct\\Cms\\Controller\\".ucfirst($type)."Controller";

        if (class_exists($controlClassName))  {

            $object = new $controlClassName();

            if (method_exists($object, $methodName)) {
                return $object->{$methodName}($params);
            }
        }

        return null;
    }
}
