<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Icon;

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
        return "<span class='iconify menu-tab-icon icon-plus' data-icon='akar-icons:plus'></span>";
    }

    /**
     * @return string
     */
    public function edit(): string
    {
        return "<span class='iconify menu-tab-icon icon-edit' data-icon='fa-solid:edit'></span>";
    }

    /**
     * @return string
     */
    public function delete(): string
    {
        return "<span class='iconify menu-tab-icon icon-delete' data-icon='ic:round-delete-forever'></span>";
    }

    public function arrowBack(): string
    {
        return "<span class='iconify' style='font-size: 2em; cursor: pointer;' data-icon='akar-icons:arrow-back-thick' onclick='history.back();'></span>";
    }
}
