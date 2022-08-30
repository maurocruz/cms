<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Helpers;

class Helper
{
    /**
     * @return BreadcrumbList
     */
    public static function breadcrumb(): BreadcrumbList
    {
        return new BreadcrumbList();
    }
}