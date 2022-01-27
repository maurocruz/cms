<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Helpers;

use Exception;
use Plinct\Cms\App;
use Plinct\Tool\FileSystem\FileSystem;
use Plinct\Tool\Image\Image;
use Plinct\Tool\StringTool;
use Plinct\Web\Resource\Resource;
use SimpleXMLElement;

class ImageObjectUpload
{
    /**
     * @throws Exception
     */
    public static function uploadImages($imagesUploaded, $destination = ''): array
    {
        $destinationFolder = $destination == '' ? App::getImagesFolder() : $destination;
        $newParams = [];

        // NUMBER OF IMAGES
        $numberOfImages = count($imagesUploaded['name']);

        // LOOP
        for ($i=0; $i<$numberOfImages; $i++) {
            $name = $imagesUploaded['name'][$i];
            $type = $imagesUploaded['type'][$i];
            $tmp_name = $imagesUploaded['tmp_name'][$i];
            $error = $imagesUploaded['error'][$i];
            $size = $imagesUploaded['size'][$i];

            if ($error === 0 && $size !== 0 && is_uploaded_file($tmp_name)) {
                // IMAGE UPLOAD AND SET INFO FOR DATABASE
                if ($type !== "image/svg+xml") {
                    // DESTINATION FILE
                    $destinationFile = self::newImageFile($destination, $type, $name);
                    $imageTemp = new Image($tmp_name);

                    // IF IMAGE WIDTH > MAX WIDTH DAFAULT
                    if ($imageTemp->getWidth() > App::getImageMaxWidth()) {
                        $imageTemp->resize(App::getImageMaxWidth())->saveToFile($destinationFile);

                    } else {
                        FileSystem::makeDirectory($destinationFolder, 0777, true);
                        if (!move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $destinationFile)) {
                            die("error");
                        }
                    }

                    // CREATE THUMBNAIL
                    $newImage = new Image($destinationFile);
                    $newImage->thumbnail(200);
                    $newParams[$i]['contentUrl'] = $newImage->getSrc();
                    $newParams[$i]['contentSize'] = $newImage->getFileSize();
                    $newParams[$i]['thumbnail'] = $newImage->getThumbSrc();
                    $newParams[$i]['width'] = $newImage->getWidth();
                    $newParams[$i]['height'] = $newImage->getHeight();
                    $newParams[$i]['encodingFormat'] = $newImage->getEncodingFormat();
                }

                // SVG
                else {
                    // DESTINATION FILE
                    $destinationFile = self::newImageFile($destination, 'image/svg', $name);
                    FileSystem::makeDirectory($destinationFolder, 0777, true);

                    if (!move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $destinationFile)) {
                        die("error");
                    }

                    $contentUrl = (new Resource())->getHostWithSchema().$destinationFile;
                    $svg = new SimpleXMLElement(file_get_contents($_SERVER['DOCUMENT_ROOT'].$destinationFile));
                    $width = (array) $svg->attributes()['width'];
                    $height = (array) $svg->attributes()['height'];
                    $newParams[$i]['contentUrl'] = $contentUrl;
                    $newParams[$i]['contentSize'] = $size;
                    $newParams[$i]['thumbnail'] = $contentUrl;
                    $newParams[$i]['width'] = $width[0];
                    $newParams[$i]['height'] = $height[0];
                    $newParams[$i]['encodingFormat'] = "image/svg+xml";

                }
            }
        }

        return $newParams;
    }

    /**
     * @param $destination
     * @param $type
     * @param $name
     * @return string
     */
    private static function newImageFile($destination, $type, $name): string
    {
        $prefix = date("Y-m-d_H:i:s_");
        $extension = substr(strstr($type,"/"),1);
        $filename = pathinfo($name)['filename'];

        $newName = $prefix . md5(StringTool::removeAccentsAndSpaces($filename)) . "." . $extension;
        $destinationFolder = substr($destination, -1) == "/" ? $destination : $destination . DIRECTORY_SEPARATOR ;

        return $destinationFolder . $newName;
    }
}