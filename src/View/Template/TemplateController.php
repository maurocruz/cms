<?php
namespace Plinct\Cms\View\Template;

use Plinct\Cms\App;
use Plinct\Cms\Controller\Controller;
use Plinct\Cms\View\Page\IndexView;
use Plinct\Cms\View\View;
use Plinct\Tool\Locale;
use Plinct\Web\Render;

class TemplateController extends TemplateView {

    public function __construct() {
        parent::__construct();
        // TRANSLATE BY GETTEXT
        Locale::translateByGettext(App::getLanguage(), "fwc", __DIR__."/../../Locale");
        // HEAD
        parent::head();
        // HEADER
        parent::header();
        // STATUS BAR && NAVIGATION MENU
        if (isset($_SESSION['userLogin']['admin'])) {
            // user bar
            parent::setUserBar();
            // navbar
            parent::navbar();
        }
        // FOOTER
        parent::footer();
    }

    public function getContent($request) {
        $type = $request->getAttribute('type') ?? $request->getQueryParams()['type'] ?? null;
        $action = $request->getAttribute('action') ?? $request->getQueryParams()['action'] ?? "index";
        $id = $request->getAttribute('identifier') ?? $request->getQueryParams()['id'] ?? null;
        $params = $request->getQueryParams();
        if ($id) {
            $params['id'] = $id;
        }
        if($type) {
            $controller = new Controller();
            $data = $controller->getData($type, $action, $params);
            $view = (new View())->view($type, $action, $data);
            if (isset($view['navbar'])) {
                foreach ($view['navbar'] as $value) {
                    parent::addNavBar($value);
                }
            }
            parent::append("main", $view['main']);
        } else {
            $content = (new IndexView())->view();
            parent::append("main", $content['main']);
        }
    }

    public function ready(): string {
        if (!App::getTitle()) {
            parent::warning(_("You need to set site name on index.php!"));
        }
        // TITLE
        parent::setTitle();
        // MOUNT ELEMENTS
        parent::simpleMain();
        // JS
        $this->html['content'][1]['content'][] = '<script src="'.App::getStaticFolder().'/js/plinctcms.js" data-apiHost="'.App::getApiHost().'" data-staticFolder="'.App::getStaticFolder().'"></script>';
        // RETURN
        return "<!DOCTYPE html>" . Render::arrayToString($this->html);
    }
}