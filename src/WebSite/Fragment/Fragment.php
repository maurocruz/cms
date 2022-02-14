<?php
/**
 * FACTORY FOR FRAGMENTS HTML FROM CMS PLINCT PROJECT
 */

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment;

use Plinct\Cms\Authentication\AuthFragment;
use Plinct\Cms\WebSite\Fragment\Box\Box;
use Plinct\Cms\WebSite\Fragment\Box\BoxInterface;
use Plinct\Cms\WebSite\Fragment\Button\Button;
use Plinct\Cms\WebSite\Fragment\Error\Error;
use Plinct\Cms\WebSite\Fragment\Error\ErrorInterface;
use Plinct\Cms\WebSite\Fragment\Form\Form;
use Plinct\Cms\WebSite\Fragment\ListTable\ListTable;
use Plinct\Cms\WebSite\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\WebSite\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\WebSite\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\WebSite\Fragment\Navbar\NavbarFragment;
use Plinct\Cms\WebSite\Fragment\Navbar\NavbarFragmentInterface;
use Plinct\Web\Fragment\IconsFragment;

class Fragment
{
    /**
     * @return AuthFragment
     */
    public static function auth(): AuthFragment
    {
        return new AuthFragment();
    }

    /**
     * @return BoxInterface
     */
    public static function box(): BoxInterface
    {
        return new Box();
    }

    public static function button(): Button
    {
        return new Button();
    }
    /**
     * @return ErrorInterface
     */
    public static function error(): ErrorInterface
    {
        return new Error();
    }

    /**
     * @param array|null $attributes
     * @return Form
     */
    public static function form(array $attributes = null): Form
    {
        return new Form($attributes);
    }

    /**
     * @return IconsFragment
     */
    public static function icon(): IconsFragment
    {
        return \Plinct\Web\Fragment\Fragment::icons();
    }

    /**
     * @param array|null $attributes
     * @return ListTableInterface
     */
    public static function listTable(array $attributes = null): ListTableInterface
    {
        return new ListTable($attributes);
    }

    /**
     * @return MiscellaneousInterface
     */
    public static function miscellaneous(): MiscellaneousInterface
    {
        return new Miscellaneous();
    }

    /**
     * @return NavbarFragmentInterface
     */
    public static function navbar(): NavbarFragmentInterface
    {
        return new NavbarFragment();
    }

    /**
     * @param string|null $message
     * @return array
     */
    public static function noContent(string $message = null): array
    {
        $misc = new Miscellaneous();
        $mess = $message ?? _("No content");
        return $misc->message($mess);
    }
}
