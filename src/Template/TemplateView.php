<?php
namespace Plinct\Cms\Template;

use Plinct\Cms\App;

class TemplateView extends TemplateWidget {

    protected function head() {
        parent::append("head", [
            [ "tag" => "meta", "attributes" => [ "charset" => "UTF-8" ] ],
            [ "tag" => "meta", "attributes" => [ "name" => "viewport", "content" => "width=device-width" ] ],
            [ "tag" => "link", "attributes" => [ "rel" => "shortcut icon", "href" => "/portal/public/images/icons/favicon.ico", "type" => "image/x-icon" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "https://fonts.googleapis.com/icon?family=Material+Icons",'rel'=>'stylesheet' ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/reset", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/estilos", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/style-dark", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/style", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "script", "attributes" => [ "src" => "/admin/assets/js/scripts" ] ]
        ]);
    }

    protected function header() {
        // TITLE
        $apiHost = App::getApiHost();
        $apiLocation = $apiHost && filter_var($apiHost, FILTER_VALIDATE_URL) ? '<a href="' . $apiHost . '" target="_blank">' . $apiHost . '</a>' : "localhost";
        parent::append("header", [ "tag" => "p", "attributes" => [ "style" => "display: inline;" ],  "content" =>
            '<a href="/admin" style="font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;">' . App::getTitle() . '</a> '. _("Control Panel")
            . '. Api: '. $apiLocation
            . ". " . _("Version") . ": " . App::getVersion()
        ]);
        // LOG IN OUT
        if (!isset($_SESSION['userLogin'])) {
            parent::append("header", [ "tag" => "p", "attributes" => [ "style" => "float: right;" ], "content" => '<a href="/admin/login">'._("Log in").'</a>' ]);
        } else {
            parent::append("header", [
                "tag" => "p",
                "content" => '<a href="/admin/logout">'._("Log out").'</a>',
                "attributes" => [ "style" => "float: right;" ]
            ]);
        }
    }

    protected function setTitle() {
        parent::append("head", [ "tag" => "title", "content" => _("Painel CMS [ ".App::getTitle()." ]") ]);
    }

    protected function setUserBar() {
        parent::append("header", [ "tag"=>"div", "attributes" => ["class"=>"admin admin-bar-top"], "content" => [
            [ "tag"=>"p", "content" => sprintf(_("Hello, %s. You logged with %s!"), $_SESSION['userLogin']['name'], $_SESSION['userLogin']['admin'] ? "admin" : "user") ],
            [ "tag"=>"p", "content"=> _("Log out"), "href"=>"/admin/logout" ]
        ]
        ],0);
    }

    public function warning($message) {
        parent::append('main',[ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $message ]);
    }

    /**
     * LOGIN
     * @param null $auth
     */
    public function login($auth = null) {
        if ($auth && $auth['status'] == "Access unauthorized") {
            if ($auth['data'] == "Invalid email") parent::append('main', ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but this email is invalid!")]);
            if ($auth['data'] == "User not exists") parent::append('main', ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry but user not exists!")]);
            if ($auth['data'] == "User exists") parent::append('main', ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Check your data.")]);
            if ($auth['data'] == "User exists but not admin") parent::append('main', ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Sorry. The user exists but is not authorized. Contact administrator.")]);
        }
        parent::append('main', parent::formLogin());
    }

    /**
     * REGISTER FORM
     * @param string|null $warning
     */
    public function register(string $warning = null) {
        if ($warning) {
            switch ($warning) {
                case "repeatPasswordNotWork":
                    parent::append("main", ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Repeating the password doesn't work!")]);
                    parent::append("main", parent::formRegister());
                    break;
                case "emailExists":
                    parent::append("main", ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("This email already exists in our database!")]);
                    parent::append("main", parent::formRegister());
                    break;
                case "userAdded":
                    parent::append("main", ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Your registration was successful!") . "<br>" . _("Wait for confirmation from the administrator!")]);
                    parent::append("main", parent::formLogin());
                    break;
                case "error":
                    parent::append("main", ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Sorry! Something is wrong!")]);
                    parent::append("main", parent::formRegister());
                    break;
                default:
                    parent::append("main", ["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration error!") . "<br>" . _($warning)]);
                    parent::append("main", parent::formRegister());
                    break;
            }
        } else {
            parent::append("main", parent::formRegister());
        }
    }

    protected function footer() {
        parent::append("footer", [ "tag" => "p", "content" => "Copyright by Mauro Cruz" ]);
    }
}