<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server;

use Plinct\Cms\App;

class Sitemap
{
    /**
     * @param $type
     * @param null $params
     */
    public static function create($type, $params = null)
    {
        $classController = "\\Plinct\\Cms\\WebSite\\Type\\" . ucfirst($type). "\\" . ucfirst($type)."Controller";

        if (class_exists($classController)) {
            $objectController = new $classController();

            if (method_exists($objectController, "saveSitemap")) {
                $objectController->saveSitemap($params);
            }
        }
    }

    /**
     * @param null $dir
     * @return array
     */
    public function getSitemaps($dir = null): array
    {
        $sitemaps = null;
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

    /**
     * @param $sitemaps
     * @return mixed
     */
    private function typesWithSitemap($sitemaps)
    {
        $i = 1;
        libxml_use_internal_errors(true);

        foreach (App::getTypesEnabled() as $type) {
					$sitemapFilename = is_string($type) ? $this->exists_sitemap($sitemaps, $type) : null;

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

    /**
     * @param $sitemaps
     * @param $type
     * @return mixed|null
     */
    private function exists_sitemap($sitemaps, $type)
    {
        if ($sitemaps) {
            foreach ($sitemaps as $sitemapsValue) {
                $basename = basename($sitemapsValue, ".xml");
                $sitemaName = strstr($basename, '-') !== false ? strstr($basename, '-') : $basename;
                if (lcfirst($type) === substr($sitemaName, 1)) {
                    return $sitemapsValue;
                }
            }
        }

        return null;
    }
}
