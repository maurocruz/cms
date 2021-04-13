<?php
namespace Plinct\Cms\View\Widget;

class Form {
    private $form;
    private $content = [];

    public function __construct(array $attributes = null) {
        $this->form = [ "tag" => "form", "attributes" => $attributes ];
    }

    public function __set($name, $value): Form {
        $this->setAttributes([ $name => $value ]);
        return $this;
    }

    public function getForm(): array {
        $this->form['content'] = $this->content;
        return $this->form;
    }

    private function setAttributes($attributes) {
        end($this->content);
        $key = key($this->content);
        $lastAttribute = $this->content[$key]['attributes'];
        if(!$lastAttribute) {
            $this->content[$key]['attributes'] = $attributes;
        } else {
            $this->content[$key]['attributes'] = array_merge($this->content[$key]['attributes'], $attributes);
        }
    }

    private function setContent($content) {
        end($this->content);
        $key = key($this->content);
        $this->content[$key]['content'][] = $content;
    }

    public function input($attributes = null): Form {
        $this->content[] = [ "tag" => "input", "attributes" => $attributes ];
        return $this;
    }

    public function addName($name): Form {
        $this->setAttributes([ "name" => $name ]);
        return $this;
    }
    public function addType($name): Form {
        $this->setAttributes([ "type" => $name ]);
        return $this;
    }

    public function addValue($name): Form {
        $this->setAttributes([ "value" => $name ]);
        return $this;
    }

    public function fieldset($attributes = null): Form {
        $this->content[] = [ "tag" => "fieldset", "attributes" => $attributes ];
        return $this;
    }

    public function addLegend($content): Form {
        $this->setContent([ "tag" => "legend", "content" => $content ]);
        return $this;
    }

    public function addInput($attributes = null): Form {
        $this->setContent([ "tag" => "input" ]);
        return $this;
    }

}