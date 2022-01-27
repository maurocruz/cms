<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite;

use Plinct\Cms\App;
use Plinct\Cms\Factory\ViewFactory;
use Plinct\Cms\WebSite\Type\Controller;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Cms\WebSite\Structure\Structure;
use Plinct\Tool\Locale;
use Plinct\Web\Debug\Debug;
use Plinct\Web\Render;
use ReflectionException;

class WebSite extends WebSiteAbstract
{
    /**
     * @return void
     */
    public static function create()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // LANGUAGE
        self::$HTML['attributes'] = ["lang" => Locale::getServerLanguage()];
        // TRANSLATE BY GETTEXT
        Locale::translateByGettext(App::getLanguage(), "plinctCms", __DIR__."/../../Locale");
        // HEAD
        parent::addHead(Structure::head());
        // HEADER
        parent::addHeader(Structure::header());
        // FOOTER
        parent::addFooter(Structure::footer());
    }

    /**
     * @param string $message
     * @return void
     */
    public static function warning(string $message)
    {
        parent::addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $message ]);
    }

    /**
     * @throws ReflectionException
     */
    public static function getContent(array $params = null, array $queryStrings = null)
    {
        $type = $queryStrings['type'] ?? $params['type'] ?? null;
        $methodName =  $params['methodName'] ?? $queryStrings['part'] ?? $queryStrings['action'] ?? 'index';
        $id = $queryStrings['id'] ?? $params['id'] ?? null;

        if($id && $methodName == 'index') $methodName = 'edit';

        if ($type == 'closure') {
            $className = "\\" . base64_decode($queryStrings['ns']) . "\\" . $methodName;
            if (class_exists($className)) {
                $classObject = new $className();
                if (method_exists($classObject, 'viewMain')) {
                    View::contentHeader($classObject->navBar());
                    View::main($classObject->viewMain());
                }
            }

        } elseif ($type) {
            $controller = new Controller();
            $data = $controller->getData($type, $methodName, $id, $queryStrings);

            $view = new View();
            $view->view($type, $methodName, $data);

        } else {
            parent::addMain("<p>Control Panel CMSCruz - version " . App::getVersion() . ".</p>" );
        }
    }

    /**
     * @return string
     */
    public static function ready(): string
    {

        parent::$CONTENT['content'][] = self::$HEADER;
        parent::$CONTENT['content'][] = self::$MAIN;
        parent::$CONTENT['content'][] = self::$FOOTER;
        parent::$BODY['content'][] = self::$CONTENT;

        parent::$BODY['content'][] = '<script>window.apiHost = "'.App::getApiHost().'"; window.staticFolder = "'.App::getStaticFolder().'";</script>';
        parent::$BODY['content'][] = '<script src="'.App::getStaticFolder().'js/plinctcms.js" data-apiHost="'.App::getApiHost().'" data-staticFolder="'.App::getStaticFolder().'"></script>';

        parent::$HTML['content'][] = self::$HEAD;
        parent::$HTML['content'][] = self::$BODY;
        // RETURN
        return "<!DOCTYPE html>" . Render::arrayToString(parent::$HTML);
    }
}