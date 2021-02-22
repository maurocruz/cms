<?php
namespace Plinct\Cms\View\Html\Widget;

use Plinct\Cms\App;

class SitemapWidget
{
    use FormElementsTrait;

    public function index($sitemaps): array {
        foreach ($sitemaps as $valueRow) {
            // vars
            $type = $valueRow['type'];
            $file = $valueRow['file'];
            $errors = $valueRow['errors'];
            $errorText = $errors ? ' <span style="color: red;">ERROR!</span> ' : NULL;
            $extension = $valueRow['extension'] ?? "simple";
            $link = $file ? sprintf('<a href="%s/%s" target="_blank">%s</a>%s', App::$HOST, $file, $file, $errorText) : null;
            $text = $file ? "Update sitemap" : "Create sitemap";
            // items
            $item[] = [ "tag" => "p", "attributes" => [ "style" => "display: inline-block; "], "content" => _("Type").": ".$extension ];
            $item[] = [ "tag" => "button", "attributes" => [ "style" => "margin-left: 5px; height: 30px;" ], "content" => _($text) ];
            $item[] = $errors ? [ "tag" => "p", "attributes" => [ "style" => "color: red; background-color: black; padding: 7px 12px; font-weight: bold; text-align: center; display: inline-block;" ], "content" => ($errors[0])->message ] : null;
            // box items
            $div[] = self::divBoxExpanding(sprintf('%s %s', _($type), $link ), $type, [
                self::form("/admin/".lcfirst($type)."/sitemap", $item )
            ]);
            unset($item);
        }
        return self::divBox(_("Types"), "sitemap", $div);
    }
}