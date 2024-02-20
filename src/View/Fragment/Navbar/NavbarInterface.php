<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Navbar;

interface NavbarInterface
{
	public function setTitle(?string $title): NavbarInterface;
	public function setSearch(?array $search): NavbarInterface;
    /**
     * @param string $type
     * @return NavbarInterface
     */
    public function type(string $type): NavbarInterface;

    /**
     * @param int $level
     * @return NavbarInterface
     */
    public function level(int $level): NavbarInterface;

    /**
     * @param string $link
     * @param string $text
     * @return NavbarInterface
     */
    public function newTab(string $link, string $text): NavbarInterface;

    /**
     * @param string $title
     * @return NavbarInterface
     */
    public function title(string $title): NavbarInterface;

    /**
     * @param $content
     * @return NavbarInterface
     */
    public function content($content): NavbarInterface;

    /**
     * @param string $action
     * @param string $searchBy
     * @param string|null $params
     * @return NavbarInterface
     */
    public function search(string $action, string $searchBy = 'name', string $params = null): NavbarInterface;

    /**
     * @return array
     */
    public function ready(): array;
}
