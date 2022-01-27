<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Structure\Header;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Type\Structure\Header\NavBar\NavbarView;
use Plinct\Web\Element\Element;

class HeaderView extends HeaderViewAbstract implements HeaderViewInterface
{

    public function __construct()
    {
        if (self::$HeaderElement === null) {
            self::$HeaderElement = new Element('header',['class'=>'header','id'=>'header']);
        }
    }

    /**
     *
     */
   /* public static function userBar()
    {
        $div = new Element('div',["class"=>"admin admin-bar-top"]);
        $p1 = new Element('p', null, sprintf(_("Hello, %s. You logged with %s!"), $_SESSION['userLogin']['name'], $_SESSION['userLogin']['admin'] ? "admin" : "user") );
        $p2 = new Element('p', null, _("Log out"));
        $p2->href("/admin/logout");
        $div->content($p1->ready());
        $div->content($p2->ready());
        self::content($div->ready());
    }*/

    /**
     * @param string|null $title
     * @param array|null $list
     * @param int $level
     * @param array|null $searchInput
     */
    public static function navbar(string $title = null, array $list = null, int $level = 1, array $searchInput = null)
    {
        $navbar = new NavbarView();
        // TITLE
        $navbar->setTitle($title);
        // LIST
        $navbar->setList($list);
        // LEVEL
        $navbar->setLevel($level);
        // SEARCH
        $navbar->setSearch($searchInput);
        // READY
        self::content($navbar->ready());
    }

    /**
     *
     */
    public static function titleSite()
    {
        $apiHost = App::getApiHost();
        $apiLocation = $apiHost && filter_var($apiHost, FILTER_VALIDATE_URL) ? '<a href="' . $apiHost . '" target="_blank">' . $apiHost . '</a>' : "localhost";
        self::content('<p style="display: inline;"><a href="/admin" style="font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;">' . App::getTitle() . '</a> '. _("Control Panel") . '. Api: '. $apiLocation . ". " . _("Version") . ": " . App::getVersion() . '</p>');
    }

    /**
     * @return array
     */
    public static function ready(): array
    {
        return self::$HeaderElement->ready();
    }
}