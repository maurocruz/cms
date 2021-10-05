<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Cms\App;
use Plinct\Cms\Controller\Controller;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\View;
use Plinct\Tool\Locale;
use Plinct\Web\Render;

class TemplateController extends TemplateView
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
     */
    public function viewContent(array $params = null, array $queryStrings = null)
    {
        $type = $queryStrings['type'] ?? $params['type'] ?? null;
        $methodName =  $params['methodName'] ?? $queryStrings['part'] ?? $queryStrings['action'] ?? 'index';
        $id = $queryStrings['id'] ?? $params['id'] ?? null;

        if($id && $methodName == 'index') $methodName = 'edit';

        if ($type) {
            $controller = new Controller($type);
            $data = $controller->getData($type, $methodName, $id, $queryStrings);

            $view = new View();
            $view->view($type, $methodName, $data);

        } else {
            View::main([ "tag" => "p", "content" => "Control Panel CMSCruz - version " . App::getVersion() ] );
        }
    }

    /**
     * LOGIN
     * @param null $auth
     */
    public function login($auth = null)
    {
        if ($auth && $auth['status'] == "Access unauthorized") {
            if ($auth['data'] == "Invalid email") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but this email is invalid!")]);
            if ($auth['data'] == "User not exists") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but user not exists!")]);
            if ($auth['data'] == "User exists") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Check your data.")]);
            if ($auth['data'] == "User exists but not admin") MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Contact administrator.")]);
        }

        MainView::content(parent::formLogin());
    }

    /**
     * @return string
     */
    public function ready(): string
    {
        // HEADER
        $this->append('content', HeaderView::ready());

        // MAIN
        $this->append('content', (new MainView())->render());

        if (!App::getTitle()) {
            self::warning(_("You need to set site name on index.php!"));
        }

        // TITLE
        self::setTitle();

        // MOUNT ELEMENTS
        self::simpleMain();

        // JS
        $this->html['content'][1]['content'][] = '<script>window.apiHost = "'.App::getApiHost().'"; window.staticFolder = "'.App::getStaticFolder().'";</script>';
        $this->html['content'][1]['content'][] = '<script src="'.App::getStaticFolder().'/js/plinctcms.js" data-apiHost="'.App::getApiHost().'" data-staticFolder="'.App::getStaticFolder().'"></script>';

        // RETURN
        return "<!DOCTYPE html>" . Render::arrayToString($this->html);
    }
}
