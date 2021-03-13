<?php

namespace Plinct\Web\Widget;

use Plinct\Tool\StructuredData;

trait HtmlElementTrait
{    
    protected static function simpleTag(string $tag, $content = null, array $attributes = null): array {
        return [ "tag" => $tag, "attributes" => $attributes, "content" => $content ];
    } 
    
    protected static function noContent(): array {
        return self::simpleTag("p", _("No content"), [ "class" => "warning"]);
    }

    protected static function arrowBack(): array {
        return [ "object" => "image", "src" => "/App/static/cms/images/arrowBack.svg", "attributes" => [ "style" => "height: 48px; margin: 15px;"], "href" => "javascript: history.back();" ];
    }

    protected static function scriptForJsonLd(array $value): array {
        return [ "tag" => "script", "attributes" => [ "type" => "application/ld+json"], "content" => (new StructuredData())->getJson($value) ];
    }
}
