<?php
namespace Plinct\Cms\Server;

use Plinct\Cms\App;

class Sitemap
{
    public static function create($type, $params = null) {
        $classController = "\\Plinct\\Cms\\Controller\\" . ucfirst($type) . "Controller";
        if (class_exists($classController)) {
            $objectController = new $classController();
            if (method_exists($objectController, "saveSitemap")) {
                $objectController->saveSitemap($params);
            }
        }
    }

    public function getSitemaps($dir = null): array {
        $root = $dir ?? $_SERVER['DOCUMENT_ROOT'];
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
        return $this->typesWithSitemap($sitemaps);
    }

    private function typesWithSitemap($sitemaps) {
        $i = 1;
        libxml_use_internal_errors(true);
        foreach (App::$TypesEnabled as $type) {
            $sitemapFilename = $this->exists_sitemap($sitemaps, $type);

            if ($sitemapFilename) {
                $fileXml = $_SERVER['DOCUMENT_ROOT']."/".$sitemapFilename;
                $xmlFile = simplexml_load_file($fileXml);
                $namespaces = $xmlFile->getNamespaces(true);
                $extension = array_key_exists('news',$namespaces) ? "news" : "simple";
                $getError = libxml_get_errors();
                $errors = !empty($getError) ? $getError : null;
                $stmp[$type] = [ "type" => $type, "file" => $sitemapFilename, "extension" => $extension, "errors" => $errors ];
            } else {
                $stmp[$i] = [ "type" => $type, "file" => null, "extension" => null, "errors" => null ];
                $i++;
            }
            libxml_clear_errors();
        }
        ksort($stmp);
        return $stmp;
    }

    private function exists_sitemap($sitemaps, $type) {
        foreach ($sitemaps as $sitemapsValue) {
            if (lcfirst($type) === substr(strstr(basename($sitemapsValue,".xml"),'-'),1)) {
                return $sitemapsValue;
            }
        }
        return null;
    }
}
