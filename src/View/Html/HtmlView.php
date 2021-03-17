<?php
namespace Plinct\Cms\View\Html;

use Plinct\Cms\View\Html\Widget\AuthForms;
use Plinct\Cms\View\locale\Locale;
use Plinct\Web\Render;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Plinct\Cms\App;

class HtmlView extends HtmlViewContent {

    public function __construct() {
        parent::__construct();
        // gettext
        Locale::setTranslate();
        // template
        parent::setTemplate();
        parent::setSiteName(App::getTitle());        
        // header
        parent::setHeader();
        // status bar
        if (isset($_SESSION['userLogin']['admin'])) {
            // user bar
            parent::setUserBar();
            // navbar
            parent::navbar();
        }
        // footer
        parent::footer();
    }
    
    public function build(Request $request): string {
        $type = $request->getAttribute('type') ?? $request->getQueryParams()['type'] ?? null;
        $action = $request->getAttribute('action') ?? $request->getQueryParams()['action'] ?? "index";
        $id = $request->getAttribute('identifier') ?? $request->getQueryParams()['id'] ?? null;
        $params = $request->getQueryParams();
        if ($id) {
            $params['id'] = $id;
        }
        if($type) {            
            $controlClassName = "\\Plinct\\Cms\\Controller\\".ucfirst($type)."Controller";
            if (class_exists($controlClassName)) {
                $controlData = (new $controlClassName($request))->{$action}($params);
                $viewClassName = "\\Plinct\\Cms\\View\\Html\\Page\\".ucfirst($type)."View";
                if (class_exists($viewClassName)) {
                    if(isset($controlData['message']) && $controlData['message'] == "No data founded") {
                        $viewData['main'][] = (new $viewClassName())->noContent();
                    } else {
                        $viewData = (new $viewClassName())->{$action}($controlData);
                    }
                    // navbar
                    if (array_key_exists('navbar', $viewData)) {
                        foreach ($viewData['navbar'] as $value) {
                            parent::addNavBar($value);
                        }
                    }
                    // main
                    if (array_key_exists('main', $viewData)) {
                        parent::addMain($viewData['main']);
                    }
                } else {
                    parent::addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => "$type type view not founded" ]);
                }
            } else {
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => "$type type not founded" ]);
            }
        } else {
            parent::root();
        }
        return $this->ready();
    }
    
    // LOGIN FORM
    public function login($token = true): string {
        // USER NOT FOUNDED
        if ($token === null) parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => "Sinto muito, mas seu email não consta em nosso banco de dados! Tente de novo ou faça um novo <a href=\"/admin/registrar\">registro</a>"]);
        // PASSWORD INVALID
        if ($token === false || $token === '') parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => "Seu email confere, mas a senha não! Tente novamente ou entre em contato com o administrador"]);
        // FORM LOGIN
        parent::addMain(file_get_contents(__DIR__ . '/Widget/signupForm.html'));
        return $this->ready();
    }
    
    // REGISTER FORM
    public function register(string $warning = null): string
    {
        if ($warning) {
            switch ($warning) {
                case "repeatPasswordNotWork":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Repeating the password doesn't work!")]);
                    break;
                case "emailExists":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("This email already exists in our database!")]);
                    break;
                case "userAdded":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Your registration was successful!") . "<br>" . _("Wait for confirmation from the administrator!")]);
                    break;
                case "error":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Sorry! Something is wrong!")]);
                    break;
                default:
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration error!") . "<br>" . _($warning)]);
                    break;
            }
        }
        
        parent::addMain(file_get_contents(__DIR__ . '/Widget/registerForm.html'));
        return $this->ready();
    }

    // ERROR
    public function error(int $code, string $message = null): string
    {
        switch ($code) {
            // table not exists
            case 1146:
                self::startApplication();
                break;

            default:
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Error code: ".$code." ".$message ]);
        }

        return $this->ready();
    }

    private function startApplication()
    {
        parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => _("Start application") ]);
        parent::addMain(AuthForms::startApplication($_POST['email'], $_POST['password']));
    }

    public function ready(): string
    {
        //parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>');
        //parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>');
        //parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>');
        //parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>');
        //parent::addBody('<script src="https://unpkg.com/axios/dist/axios.min.js"></script>');
        parent::addBody('<script src="/App/static/cms/js/plinctcms.js"></script>');
        
        return "<!DOCTYPE html>" . Render::arrayToString($this->html);
    }
}
