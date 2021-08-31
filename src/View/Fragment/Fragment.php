<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment;

use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Box\BoxInterface;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\Form\FormInterface;
use Plinct\Cms\View\Fragment\Icon\IconInterface;
use Plinct\Cms\View\Fragment\Icon\IconFragment;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\View\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\View\Fragment\Miscellaneous\MiscellaneousInterface;

class Fragment
{
    /**
     * @param array|null $attributes
     * @return ListTableInterface
     */
    public static function listTable(array $attributes = null): ListTableInterface
    {
        return new ListTable($attributes);
    }

    public static function icon(): IconInterface
    {
        return new IconFragment();
    }

    public static function box(): BoxInterface
    {
        return new Box();
    }

    public static function form(): FormInterface
    {
        return new Form();
    }

    public static function miscellaneous(): MiscellaneousInterface
    {
        return new Miscellaneous();
    }
}