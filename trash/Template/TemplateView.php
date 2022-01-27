<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Type\Structure\Header\HeaderView;
use Plinct\Cms\WebSite\Type\Structure\Main\MainView;
use Plinct\Cms\WebSite\Structure\Structure;

abstract class TemplateView extends TemplateWidget
{
    /**
     *
     */
    protected function head()
    {
        parent::append("head", Structure::head());
    }

    /**
     *
     */
    protected function header()
    {
        // USER BAR
        if (isset($_SESSION['userLogin']['admin'])) {
            //HeaderView::userBar();
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
    public function warning($message)
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
