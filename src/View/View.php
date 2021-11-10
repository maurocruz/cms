<?php

declare(strict_types=1);

namespace Plinct\Cms\View;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;

class View extends ViewAbstract implements ViewInterface
{
    /**
     * @param $content
     * @return void
     */
    public static function contentHeader($content)
    {
        HeaderView::content($content);
    }

    /**
     * @param string|null $title
     * @param array|null $list
     * @param int|null $level
     * @param array|null $searchInput
     */
    public static function navbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
    {
        HeaderView::navbar($title, $list, $level, $searchInput);
    }

    /**
     * @param $content
     */
    public static function main($content)
    {
        MainView::content($content);
    }

    /**
     * @param $type
     * @param $methodName
     * @param $data
     * @return void
     */
    public function view($type, $methodName, $data)
    {
        $className = self::NAMESPACE_VIEW . ucfirst($type) . "\\" . ucfirst($type) . "View";

        // ERROR
        $error = $data['error'] ?? null;
        if ($error) {
            switch ($error['code']) {
                case '42S02':
                    MainView::content(Fragment::error()->installSqlTable($type, $error['message']));
                    break;
                default:
                    MainView::content("<p class='warning'>{$error['message']}</p>");
            }
        }

        // VIEW
        elseif (class_exists($className)) {
            $object = new $className();
            if (method_exists($object,$methodName)) {
                $object->{$methodName}($data);
            } else {
                MainView::content("<p class='warning'>Method view not exists!</p>");
            }
        }

        // TYPE NOT FOUND
        else {
            MainView::content("<p class='warning'>$type type view not founded</p>");
        }
    }
}
