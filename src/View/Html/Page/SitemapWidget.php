<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\App;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;

class SitemapWidget
{
    use FormElementsTrait;

    public function index(): array {
        $typesWithSitemap = $this->typesWithSitemap();
        foreach ($typesWithSitemap as $valueRow) {
            $type = $valueRow['type'];
            $file = $valueRow['file'];
            $extension = $valueRow['extension'] ?? "simple";
            $link = $file ? '<a href="'.App::$HOST.'/'.$file.'" target="_blank">'.$file.'</a>' : null;
            $text = $file ? "Update sitemap" : "Create sitemap";
            $div[] = self::divBox(sprintf('%s %s', _($type), $link ), $type, [
                self::form("/admin/".lcfirst($type)."/sitemap", [
                    self::fieldsetWithSelect(_("Sitemap extension"), "sitemapExtension", $extension, [ "simple" => _("Simple"), "new" => _("News"), "video" => _("Video"), "image" => _("Image") ], null, [ "style" => "width: 120px;"]),
                    [ "tag" => "button", "attributes" => [ "style" => "margin-left: 5px; height: 30px;" ], "content" => _($text) ]
                ])
            ]);
            unset($row);
        }
        return self::divBox(_("Types"), "sitemap", $div);
    }

    private function typesWithSitemap() {
        $existSitemap = null;
        $i = 1;
        foreach (App::$TypesEnabled as $type) {
            foreach ($this->sitemapsExistents() as $sitemaps) {
                if (lcfirst($type) === substr(strstr(basename($sitemaps,".xml"),'-'),1)) {
                    $existSitemap = $sitemaps;
                }
            }
            if ($existSitemap) {
                $xmlFile = simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/".$existSitemap);
                $namespaces = $xmlFile->getNamespaces(true);
                if(array_key_exists('news',$namespaces)) {
                    $extension = "news";
                } else {
                    $extension = "simple";
                }
                $stmp[$type] = [ "type" => $type, "file" => $existSitemap, "extension" => $extension ];
            } else {
                $stmp[$i] = [ "type" => $type, "file" => null, "extension" => null ];
                $i++;
            }
            $existSitemap = null;
        }
        ksort($stmp);
        return $stmp;
    }

    private function sitemapsExistents(): array {
        $root = $_SERVER['DOCUMENT_ROOT'];
        $handleRoot = opendir($root);
        while (false !== ($filename = readdir($handleRoot))) {
            $file = $root.DIRECTORY_SEPARATOR.$filename;
            if (is_file($file)) {
                $pathInfo = pathinfo($file);
                if ($pathInfo['extension'] === 'xml') {
                    $sitemaps[] = $filename;
                }
            }
        }
        closedir($handleRoot);
        return $sitemaps;
    }
}