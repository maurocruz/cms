<?php

namespace Plinct\Cms\View\Html;
    
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Plinct\Api\Auth\SessionUser;
use Plinct\Cms\App;

class HtmlView extends HtmlViewContent
{    
    public function __construct()
    {
        // gettext
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
            // userbar
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
                    $viewData = (new $viewClassName())->{$action}($controlData);
                    
                    
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
        switch ($message) {
            case "Password invalid":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Seu email confere, mas a senha não! Tente novamente ou entre em contato com o administrador" ]);
                break;
            
            case "User not found":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Sinto muito, mas seu email não consta em nosso banco de dados! Tente de novo ou faça um novo <a href=\"/admin/registrar\">registro</a>" ]);
                break;
           
            case "userNotAuthorized":
                parent::addMain([ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => "Você está devidamente registrado, mas não tem permissão para acessar este painel. Por favor, entre em contato com o administrador!" ]);
                break;                
        }
                
        parent::addMain(file_get_contents(__DIR__ . '/pieces/signupForm.html'));
        
        return $this->ready();
    }
    
    // REGISTER FORM
    public function register($warning = null) 
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
    
    public function ready() 
    {
        //parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>');
        //parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>');
        parent::addBody('<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>');
        parent::addBody('<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>');
        parent::addBody('<script src="https://unpkg.com/axios/dist/axios.min.js"></script>');
        parent::addBody('<script src="/admin/assets/js/bundle"></script>');
        
        return "<!DOCTYPE html>" . \Plinct\Web\Render::arrayToString($this->html);
    }
}
