<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Cms\App;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;

abstract class TemplateView extends TemplateWidget
{
    /**
     *
     */
    protected function head()
    {
        parent::append("head", [
            [ "tag" => "meta", "attributes" => [ "charset" => "UTF-8" ] ],
            [ "tag" => "meta", "attributes" => [ "name" => "viewport", "content" => "width=device-width" ] ],
            [ "tag" => "link", "attributes" => [ "rel" => "shortcut icon", "href" => "/portal/public/images/icons/favicon.ico", "type" => "image/x-icon" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "https://fonts.googleapis.com/icon?family=Material+Icons",'rel'=>'stylesheet' ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/reset", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/estilos", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/style", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "link", "attributes" => [ "href" => "/admin/assets/css/style-dark", "type" => "text/css", "rel" => "stylesheet" ] ],
            [ "tag" => "script", "attributes" => [ "src" => "/admin/assets/js/scripts" ] ],
            [ "tag" => "script", "attributes" => [ "src" => "https://code.iconify.design/2/2.0.3/iconify.min.js" ] ]
        ]);
    }

    /**
     *
     */
    protected function header()
    {
        // USER BAR
        if (isset($_SESSION['userLogin']['admin'])) {
            HeaderView::userBar();
        }
        // TITLE
        HeaderView::titleSite();
        // FIRST MENU
        if (isset($_SESSION['userLogin']['admin'])) {
            HeaderView::navBar(null, parent::navbarList());
        }
    }

    /**
     *
     */
    protected function setTitle()
    {
        parent::append("head", [ "tag" => "title", "content" => _("Painel CMS [ ".App::getTitle()." ]") ]);
    }

    /**
     * @param $message
     */
    protected function warning($message)
    {
        MainView::content([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $message ]);
    }

    /**
     * REGISTER FORM
     * @param string|null $warning
     */
    public function register(string $warning = null)
    {
        if ($warning) {
            switch ($warning) {
                case "repeatPasswordNotWork":
                    MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Repeating the password doesn't work!")]);
                    MainView::content(parent::formRegister());
                    break;
                case "emailExists":
                    MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("This email already exists in our database!")]);
                    MainView::content(parent::formRegister());
                    break;
                case "userAdded":
                    MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Your registration was successful!") . "<br>" . _("Wait for confirmation from the administrator!")]);
                    MainView::content(parent::formLogin());
                    break;
                case "error":
                    MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration not successful!") . "<br>" . _("Sorry! Something is wrong!")]);
                    MainView::content(parent::formRegister());
                    break;
                default:
                    MainView::content(["tag" => "p", "attributes" => ["class" => "aviso"], "content" => _("Registration error!") . "<br>" . _($warning)]);
                    MainView::content(parent::formRegister());
                    break;
            }

        } else {
            MainView::content(parent::formRegister());
        }
    }

    /**
     *
     */
    protected function footer()
    {
        parent::append("footer", [ "tag" => "p", "content" => "Copyright by Mauro Cruz" ]);
    }
}
