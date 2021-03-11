<?php
namespace Plinct\Cms\View\Html;

use Plinct\Cms\App;

class HtmlViewContent extends HtmlViewAbstract {

    protected function navbar() {
        $data['list'] = [
            "/admin" => _("Home"),
            "/admin/user" => _("Users")
        ];
        if (App::getTypesEnabled()) {
            foreach (App::getTypesEnabled() as $key => $value) {
                $data['list']['/admin/'.$value] = _(ucfirst($value));
            }
        }
        $data['attributes'] = ["class"=>"menu"];
        parent::addNavBar($data);            
    } 

    protected function setTemplate() {
        $this->html['content'] = [
            [ "tag" => "head", "content" => [
                [ "tag" => "meta", "attributes" => [ "charset" => "UTF-8" ] ],
                [ "tag" => "meta", "attributes" => [ "name" => "viewport", "content" => "width=device-width" ] ],
                [ "tag" => "link", "attributes" => [ "rel" => "shortcut icon", "href" => "/portal/public/images/icons/favicon.ico", "type" => "image/x-icon" ] ],
                [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/reset", "type" => "text/css", "rel" => "stylesheet" ] ],
                [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/estilos", "type" => "text/css", "rel" => "stylesheet" ] ],
                [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/style-dark", "type" => "text/css", "rel" => "stylesheet" ] ],
                [ "tag" => "script", "attributes" => [ "src" => "/admin/assets/js/scripts" ] ],
            ]],
            [ "tag" => "body", "content" => [
                [ "tag" => "div", "attributes" => [ "class" => "wrapper" ], "content" => [
                    [ "tag" => "header", "attributes" => [ "class" => "header" ] ],
                    [ "tag" => "main", "attributes" => [ "class" => "main content" ] ],
                    [ "tag" => "footer", "attributes" => [ "class" => "footer" ] ]
                ]]
            ]]
        ];
    }
    
    protected function setUserBar() {
        parent::addHeader([ "tag"=>"div", "attributes" => ["class"=>"admin admin-bar-top"], "content" => [
                [ "tag"=>"p", "content" => sprintf(_("Hello, %s. You logged with %s!"), $_SESSION['userLogin']['name'], $_SESSION['userLogin']['admin'] ? "admin" : "user") ],
                [ "tag"=>"p", "content"=> _("Log out"), "href"=>"/admin/logout" ]
            ]            
        ],1);
    }
    
    protected function setHeader() {
        parent::addHeader([ "tag" => "p", "attributes" => [ "style" => "display: inline;" ],  "content" => "<a href=\"/admin\" style=\"font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;\">" . App::getTitle() . "</a> ". _("Control Panel") . ". " . _("Version") . ": " . App::getVersion()
        ]);        
        if (!isset($_SESSION['userLogin'])) {        
            parent::addHeader([ "tag" => "p", "attributes" => [ "style" => "float: right;" ], "content" => '<a href="/admin/login">Entrar</a>' ]);
        } else { 
            parent::addHeader([ 
                "tag" => "p", 
                "content" => '<a href="/admin/logout">Sair</a>', 
                "attributes" => [ "style" => "float: right;" ]  
            ]);
        }
    }
    
    protected function addContent($content) {
        if (is_array($content)) {
            if(array_key_exists('header', $content)) {
                parent::addHeader($content['header']);
            }
            if(array_key_exists('navbar', $content)) {
                foreach ($content['navbar'] as $valueNavBar) {
                    parent::addNavBar([
                        "list" =>$valueNavBar['list'],
                        "attributes" =>$valueNavBar['attributes'],
                        "title" => $valueNavBar['title']
                    ]);
                }
            }
            if(array_key_exists('main', $content)) {
                parent::addMain($content['main']);
            }
        } else {
            parent::addMain($content);
        }
    }
    
    protected function root() {
        $content[] = [ "tag" => "p", "content" => "Control Panel CMSCruz - version " . App::getVersion() ];
        parent::addMain([ "tag" => "p", "content" => $content ]);   
    }
    
    protected function footer() {
        parent::addFooter([ "tag" => "p", "content" => "Copyright by Mauro Cruz" ]);
    }
}
