<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Structure;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Fragment\Fragment;

class Structure
{
    public static function head(): string
    {
        return '
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width">
            <link rel="shortcut icon" href="/portal/public/images/icons/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            <link href="/admin/assets/css/reset" type="text/css" rel="stylesheet">
            <link href="/admin/assets/css/estilos" type="text/css" rel="stylesheet">
            <link href="/admin/assets/css/style" type="text/css" rel="stylesheet">
            <link href="/admin/assets/css/style-dark" type="text/css" rel="stylesheet">
            <script src="/admin/assets/js/scripts"></script>
            <script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
            <title>Plinct CMS [' . App::getTitle() . ']</title>';
    }

    /**
     * @return string
     */
    public static function userBar(): string
    {
        $helloText = sprintf(_("Hello, %s. You logged with %s!"), $_SESSION['userLogin']['name'], $_SESSION['userLogin']['admin'] ? "admin" : "user");
        return "<div class='admin admin-bar-top'>
                <p>$helloText</p>
                <p><a href='/admin/logout'>" . _("Log out") . "</a></p>
            </div>";
    }

    /**
     * @return string
     */
    public static function header(): string
    {
        $apiHost = App::getApiHost();
        $apiLocation = $apiHost && filter_var($apiHost, FILTER_VALIDATE_URL) ? '<a href="' . $apiHost . '" target="_blank">' . $apiHost . '</a>' : "localhost";
        return '<p style="display: inline;"><a href="/admin" style="font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;">' . App::getTitle() . '</a> ' . _("Control Panel") . '. Api: ' . $apiLocation . ". " . _("Version") . ": " . App::getVersion() . '</p>';
    }

    public static function mainMenu(): array
    {
        $navbar = Fragment::navbar();
        $navbar->newTab("/admin",_("Home"));
        $navbar->newTab("/admin/user",_("Users"));

        if (App::getTypesEnabled()) {
            $attributes = null;

            foreach (App::getTypesEnabled() as $key => $value) {
                if (is_string($key) && is_string($value)) {
                    $link = $key;
                    $text = ucfirst($value);
                }
                // if closure
                elseif (is_object($value)) {
                    $link = "/admin/closure/" . $value->getMenuPath();
                    $text = ucfirst($value->getMenuText());
                    $attributes['style'] = "background-color: #574141;";
                }
                else {
                    $link = "/admin/$value";
                    $text = ucfirst($value);
                }

                $navbar->newTab($link, $text, $attributes);
            }
        }

        return $navbar->ready();

    }

    public static function footer(): string
    {
        return "<p>Copyright by Mauro Cruz</p>";
    }
}
