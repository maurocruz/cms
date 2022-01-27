<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;

class View extends ViewAbstract implements ViewInterface
{
    /**
     * @param $content
     * @return void
     */
    public static function contentHeader($content)
    {
        WebSite::addHeader($content);
    }

    /**
     * @param string|null $title
     * @param array|null $list
     * @param int|null $level
     * @param array|null $searchInput
     */
    public static function navbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
    {
        $fragment = Fragment::navbar();
        $fragment->title($title)->level($level);
        if ($list) {
            foreach ($list as $key => $value) {
                $fragment->newTab($key, $value);
            }
        }

        if ($searchInput) {
            $type = $searchInput['table'] ?? null;
            if($type) $fragment->type($type);
            $fragment->search("/admin/$type/search",$searchInput['searchBy'] ?? "name", $searchInput['params'] ?? null, $searchInput['linkList'] ?? null);
        }

        WebSite::addHeader($fragment->ready());
    }

    /**
     * @param $content
     */
    public static function main($content)
    {
        WebSite::addMain($content);
    }

    /**
     * @param $type
     * @param $methodName
     * @param $data
     * @return void
     */
    public function view($type, $methodName, $data)
    {
        $className = __NAMESPACE__ . "\\" . ucfirst($type) . "\\" . ucfirst($type) . "View";

        // ERROR
        $error = $data['error'] ?? null;
        if ($error) {
            switch ($error['code']) {
                case '42S02':
                    WebSite::addMain(Fragment::error()->installSqlTable($type, $error['message']));
                    break;
                default:
                    WebSite::addMain("<p class='warning'>{$error['message']}</p>");
            }
        }

        // VIEW
        elseif (class_exists($className)) {
            $object = new $className();
            if (method_exists($object,$methodName)) {
                if (empty($data) && $methodName != "new") {
                    View::main(Fragment::noContent(_("No data found!")));
                } else {
                    $object->{$methodName}($data);
                }
            } else {
                WebSite::addMain("<p class='warning'>Method view not exists!</p>");
            }
        }

        // TYPE NOT FOUND
        else {
            WebSite::addMain("<p class='warning'>$type type view not founded</p>");
        }
    }
}
