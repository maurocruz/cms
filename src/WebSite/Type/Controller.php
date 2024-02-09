<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

use Plinct\Cms\CmsFactory;
use ReflectionException;

class Controller
{
    /**
     * @param $type
     * @param $methodName
     * @param $id
     * @param $params
     * @return mixed
     * @throws ReflectionException
     */
    public function getData($type, $methodName, $id, $params)
    {
        // IF SEND SEARCH QUERY
        if (isset($params['q'])) {
            $params['nameLike'] = $params['q'];
        }
        unset($params['type']);

        $methodName = $methodName == 'index' && isset($id) ? 'edit' : $methodName;
        if($id) $params["id$type"] = $id;

				$requestClass = CmsFactory::request()->type();
				if (method_exists($requestClass,$type)) {
					$typeClass = $requestClass->$type();
					if (method_exists($typeClass, $methodName)) {
						return $typeClass->$methodName($params);
					}
				}

				// DEPRECATED
        $controlClassName = __NAMESPACE__ . "\\" . ucfirst($type) . "\\" . ucfirst($type)."Controller";
        if (class_exists($controlClassName))  {
            $object = new $controlClassName();
            if (method_exists($object, $methodName)) {
                return $object->{$methodName}($params);
            }
        }
        return null;
    }
}
