<?php
/**
 * FACTORY FOR FRAGMENTS HTML FROM CMS PLINCT PROJECT
 */

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment;

use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Box\BoxInterface;
use Plinct\Cms\View\Fragment\Error\Error;
use Plinct\Cms\View\Fragment\Error\ErrorInterface;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\Icon\IconInterface;
use Plinct\Cms\View\Fragment\Icon\IconFragment;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\View\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\View\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\View\Fragment\Navbar\NavbarFragment;
use Plinct\Cms\View\Fragment\Navbar\NavbarFragmentInterface;

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
}
