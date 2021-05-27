<?php
namespace Plinct\Cms\View;

class View {
    const NAMESPACE_VIEW = "\\Plinct\\Cms\\View\\Types\\";

    public function view($type, $action, $data): array {
        $className = self::NAMESPACE_VIEW . ucfirst($type) . "\\" . ucfirst($type) . "View";
        $error = $data['error'] ?? null;
        // ERROR
        if ($error) return self::responseError($error['message']);
        // VIEW
        if (class_exists($className)) {
            $object = new $className();
            if (method_exists($object,$action)) {
                return self::responseView($object, $action, $data);
            } else {
                return self::responseError("Method not exists!");
            }
        }
        // TYPE NOT FOUND
        return self::responseError("$type type view not founded");
    }

    private static function responseView($object, $action, $data): array {
        $view = $object->{$action}($data);
        return [ "navbar" => $view['navbar'] ?? null, "main" => $view['main'] ?? null ];
    }

    private static function responseError(string $content): array {
            return [ "main" => [ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => _($content) ]];
    }
}