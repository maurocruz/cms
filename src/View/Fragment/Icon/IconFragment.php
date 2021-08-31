<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Icon;

class IconFragment implements IconInterface
{
    /**
     * @return string
     */
    public function home(): string
    {
        return "<span class='iconify menu-tab-icon icon-home' data-icon='ci:home-alt-fill'></span>";
    }

    /**
     * @return string
     */
    public function plus(): string
    {
        return "<span class='iconify menu-tab-icon icon-plus' data-icon='bi:plus-lg'></span>";
    }

    public function edit(): string
    {
        return "<span class='iconify menu-tab-icon icon-edit' data-icon='fa-solid:edit'></span>";
    }

    public function delete(): string
    {
        return "<span class='iconify menu-tab-icon icon-delete' data-icon='ic:round-delete-forever'></span>";
    }
}