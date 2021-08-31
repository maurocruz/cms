<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Cms\App;
use Plinct\Cms\Controller\Controller;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\View;
use Plinct\Tool\Locale;

class TemplateController extends TemplateView implements TemplateInterface
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        // TRANSLATE BY GETTEXT
        Locale::translateByGettext(App::getLanguage(), "plinctCms", __DIR__."/../../Locale");
        // HEAD
        parent::head();
        // HEADER
        new HeaderView();
        parent::header();
        // MAIN
        (new MainView())->create();
        // FOOTER
        parent::footer();
    }

    /**
     * @param array|null $params
     * @param array|null $queryStrings
     * @return string
     */
    public function viewContent(array $params = null, array $queryStrings = null): string
    {
        $type = $queryStrings['type'] ?? $params['type'] ?? null;
        $methodName =  $params['methodName'] ?? $queryStrings['part'] ?? $queryStrings['action'] ?? 'index';
        $id = $queryStrings['id'] ?? $params['id'] ?? null;

        if($id && $methodName == 'index') $methodName = 'edit';

        if ($type) {
            $controller = new Controller();
            $data = $controller->getData($type, $methodName, $id, $queryStrings);

            $view = new View();
            $view->view($type, $methodName, $data);

        } else {
            View::main([ "tag" => "p", "content" => "Control Panel CMSCruz - version " . App::getVersion() ] );
        }

        return $this->ready();
    }

    /**
     * LOGIN
     * @param null $auth
     */
    public function login($auth = null): string
    {
        if ($auth && $auth['status'] == "Access unauthorized") {
            if ($auth['data'] == "Invalid email") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but this email is invalid!")]);
            if ($auth['data'] == "User not exists") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but user not exists!")]);
            if ($auth['data'] == "User exists") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Check your data.")]);
            if ($auth['data'] == "User exists but not admin") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Contact administrator.")]);
        }
        MainView::content(parent::formLogin());

        return $this->ready();
    }
}
