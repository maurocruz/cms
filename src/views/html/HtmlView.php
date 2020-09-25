<?php

namespace Plinct\Cms\View\Html;
    
use Plinct\Cms\Views\Html\Pieces\AuthForms;
use Plinct\Web\Render;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Plinct\Api\Auth\SessionUser;
use Plinct\Cms\App;

class HtmlView extends HtmlViewContent
{    
    public function __construct()
    {
        parent::__construct();

        // gettext
        $lang = App::getLanguage();
        putenv("LC_ALL=$lang");
        setlocale(LC_ALL, App::getLanguage() . ".utf8");
        bindtextdomain("fwc", __DIR__ . "/../locale");
        textdomain("fwc");
        
        // template
        parent::setTemplate();
        // site name
        parent::setSiteName(App::getTitle());        
        // header
        parent::setHeader();
        
        // status bar
        if (SessionUser::checkUserAdmin()) {
            // user bar
            parent::setUserBar();
            // navbar
            parent::navbar();
        }
        
        // footer
        parent::footer();
    }
    
    public function build(Request $request)
    {
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
    public function login($message = null) 
    {
        if ($message) {
            switch ($message) {
                case "Password invalid":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => "Seu email confere, mas a senha não! Tente novamente ou entre em contato com o administrador"]);
                    break;

                case "User not found":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => "Sinto muito, mas seu email não consta em nosso banco de dados! Tente de novo ou faça um novo <a href=\"/admin/registrar\">registro</a>"]);
                    break;

                case "userNotAuthorized":
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => "Você está devidamente registrado, mas não tem permissão para acessar este painel. Por favor, entre em contato com o administrador!"]);
                    break;
                default:
                    parent::addMain(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => $message]);
            }
        }
                
        parent::addMain(file_get_contents(__DIR__ . '/pieces/signupForm.html'));
        
        return $this->ready();
    }
    
    // REGISTER FORM
    public function register(string $warning = null)
    {
        switch ($warning) {
            case "repeatPasswordNotWork":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "A repetição da senha não confere!" ]);
                break;
            case "emailExists":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Este email já existe em nosso banco de dados!" ]);
                break;
            case "userAdded":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Seu registro foi um sucesso! Aguarde a confirmação do administrador!" ]);
                break;
            case "error":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Desculpe! Algo está errado!" ]);
                break;
        }
        
        parent::addMain(file_get_contents(__DIR__ . '/pieces/registerForm.html'));
        return $this->ready();
    }

    // ERROR
    public function error(int $code, string $message = null)
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

    public function ready() 
    {
        //parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>');
        //parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>');
        parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>');
        parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>');
        parent::addBody('<script src="https://unpkg.com/axios/dist/axios.min.js"></script>');
        parent::addBody('<script src="/App/static/cms/js/plinctcms.js"></script>');
        
        return "<!DOCTYPE html>" . Render::arrayToString($this->html);
    }
}
