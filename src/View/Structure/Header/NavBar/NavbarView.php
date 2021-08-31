<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Structure\Header\NavBar;

use Plinct\Web\Element\Element;

class NavbarView
{
    /**
     * @var Element
     */
    protected $navbar;

    /**
     *
     */
    public function __construct()
    {
        $this->navbar = new Element('nav',['class'=>'menu']);
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title = null): void
    {
        if ($title) {
            $this->navbar->content("<h1>$title</h1>");
        }
    }

    /**
     * @param mixed $list
     */
    public function setList(array $list = null): void
    {
        if($list) {
            foreach ($list as $key => $value) {
                $this->navbar->content("<a href='$key'>$value</a>");
            }
        }
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level)
    {
        $this->navbar->attributes(['class'=>"menu menu$level"]);
    }

    /**
     * @param array|null $searchArray
     */
    public function setSearch(array $searchArray = null)
    {
        if ($searchArray) {
            $table = $searchArray['table'] ?? null;
            if ($table) {
                $property = $searchArray['searchBy'] ?? "name";
                $params = $searchArray['params'] ?? "";
                $div = new Element('div', ["class" => "navbar-search", "data-type" => $table, "data-searchBy" => $property, "data-params" => $params]);
                $this->navbar->content($div->ready());
            }
        }
    }

    /**
     * @return array
     */
    public final function ready(): array
    {
        return $this->navbar->ready();
    }
}