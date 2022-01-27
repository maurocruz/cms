<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Navbar;

interface NavbarFragmentInterface
{
    /**
     * @param string $type
     * @return NavbarFragmentInterface
     */
    public function type(string $type): NavbarFragmentInterface;

    /**
     * @param int $level
     * @return NavbarFragmentInterface
     */
    public function level(int $level): NavbarFragmentInterface;

    /**
     * @param string $link
     * @param string $text
     * @return NavbarFragmentInterface
     */
    public function newTab(string $link, string $text): NavbarFragmentInterface;

    /**
     * @param string $title
     * @return NavbarFragmentInterface
     */
    public function title(string $title): NavbarFragmentInterface;

    /**
     * @param $content
     * @return NavbarFragmentInterface
     */
    public function content($content): NavbarFragmentInterface;

    /**
     * @param string $action
     * @param string $searchBy
     * @param string|null $params
     * @return NavbarFragmentInterface
     */
    public function search(string $action, string $searchBy = 'name', string $params = null): NavbarFragmentInterface;

    /**
     * @return array
     */
    public function ready(): array;
}
