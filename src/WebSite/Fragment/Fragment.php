<?php
/**
 * FACTORY FOR FRAGMENTS HTML FROM CMS PLINCT PROJECT
 */

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment;

use Plinct\Cms\WebSite\Fragment\Box\Box;
use Plinct\Cms\WebSite\Fragment\Box\BoxInterface;
use Plinct\Cms\WebSite\Fragment\Error\Error;
use Plinct\Cms\WebSite\Fragment\Error\ErrorInterface;
use Plinct\Cms\WebSite\Fragment\Form\Form;
use Plinct\Cms\WebSite\Fragment\Icon\IconInterface;
use Plinct\Cms\WebSite\Fragment\Icon\IconFragment;
use Plinct\Cms\WebSite\Fragment\ListTable\ListTable;
use Plinct\Cms\WebSite\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\WebSite\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\WebSite\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\WebSite\Fragment\Navbar\NavbarFragment;
use Plinct\Cms\WebSite\Fragment\Navbar\NavbarFragmentInterface;
use Plinct\Cms\WebSite\Fragment\User\UserFragment;

class Fragment
{
    /**
     * @return BoxInterface
     */
    public static function box(): BoxInterface
    {
        return new Box();
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
     * @return IconInterface
     */
    public static function icon(): IconInterface
    {
        return new IconFragment();
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

    /**
     * @return UserFragment
     */
    public static function user(): UserFragment
    {
        return new UserFragment();
    }
}
