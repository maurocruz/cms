<?php

declare(strict_types=1);

namespace Plinct\Cms\View;

use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;

class View extends ViewAbstract implements ViewInterface
{
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

        $error = $data['error'] ?? null;
        // ERROR
        if ($error) MainView::content("<p class='warning'>{$error['message']}</p>");

        // VIEW
        if (class_exists($className)) {
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
