<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment;

use Plinct\Web\Element\ElementInterface;

class ElementDecorator implements ElementInterface
{
    /**
     * @var ElementInterface
     */
    protected ElementInterface $element;

    /**
     * @param $content
     * @return ElementInterface
     */
    public function content($content): ElementInterface
    {
        $this->element->content($content);
        return $this->element;
    }

    /**
     * @param array|null $attributes
     * @return ElementInterface
     */
    public function attributes(array $attributes = null): ElementInterface
    {
        $this->element->attributes($attributes);
        return $this->element;
    }

    /**
     * @param $name
     * @param $value
     * @return ElementInterface
     */
    public function setAttribute($name, $value): ElementInterface
    {
       $this->element->setAttribute($name, $value);
       return $this->element;
    }

    /**
     * @param string $href
     * @return ElementInterface
     */
    public function href(string $href): ElementInterface
    {
        $this->element->href($href);
        return $this->element;
    }

    /**
     * @return array
     */
    public function ready(): array
    {
        return $this->element->ready();
    }
}
