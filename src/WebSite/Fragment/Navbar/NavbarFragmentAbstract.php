<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Navbar;

abstract class NavbarFragmentAbstract implements NavbarFragmentInterface
{
    /**
     * @var ?string
     */
    protected ?string $type = null;
    /**
     * @var array
     */
    protected array $wrapper;



    /**
     * @param string $type
     */
    protected function setType(string $type): void
    {
        $this->type = $type;
    }
    /**
     */
    protected function setWrapper(): void
    {
        $this->wrapper = ['tag'=>'nav', 'attributes'=>['class'=>'menu menu2']];
    }

    /**
     * @param string $name
     * @param $value
     */
    protected function setAttributes(string $name, $value): void
    {
        $this->wrapper['attributes'][$name] = $value;
    }

    public function content($content): NavbarFragmentInterface
    {
        $this->wrapper['content'][] = $content;
        return $this;
    }
}