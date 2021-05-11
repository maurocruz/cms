<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\PDO\PDOConnect;
use Plinct\Tool\FileSystem\FileSystem;
use Plinct\Tool\Image\Image;
use Plinct\Tool\StringTool;

class ImageObjectServer {
    private $tablesHasImageObject;
    private static $KEYWORDS_LIST;
    private static $KEYWORDS;
    private static $LIST_LOCATIONS;

    public function __construct() {
        $table_schema = PDOConnect::getDbname();
        $this->tablesHasImageObject = PDOConnect::run("select table_name as tableName from information_schema.tables WHERE table_schema = '$table_schema' AND table_name LIKE '%_has_imageObject';");
    }

    public function new($params) {
        // UPLOAD IMAGE
        if (isset($_FILES['imageupload'])) {
            if ($_FILES['imageupload']['size'][0] === 0) {
                return filter_input(INPUT_SERVER, 'HTTP_REFERER');
            }
            $newParams = ImageObjectServer::uploadImages($_FILES['imageupload'], $params['location']);
            unset($params['location']);
            foreach ($newParams as $valueNewParams) {
                $params = array_merge($params, $valueNewParams);
                Api::post("imageObject", $params);
            }
        }
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }


    public static function uploadImages($imagesUploaded, $destination = ''): array {
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
                // DESTINATION FILE
                $destinationFile = self::newImageFile($destination, $type, $name);
                // IMAGE CLASS
                $imageTemp = new Image($tmp_name);
                // IF IMAGE WIDTH > MAX WIDTH DAFAULT
                if ($imageTemp->getWidth() > App::getImageMaxWigth()) {
                    $imageTemp->resize(App::getImageMaxWigth())->saveToFile($destinationFile);
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
        }
        return $newParams;
    }

    private static function newImageFile($destination, $type, $name): string {
        $prefix = date("Y-m-d_H:i:s_");
        $extension = substr(strstr($type,"/"),1);
        $filename = pathinfo($name)['filename'];
        $newName = $prefix . md5(StringTool::removeAccentsAndSpaces($filename)) . "." . $extension;
        $destinationFolder = substr($destination, -1) == "/" ? $destination : $destination . DIRECTORY_SEPARATOR ;
        return $destinationFolder . $newName;
    }

    public function getImageHasPartOf($idIsPartOf): ?array {
        $info = null;
        foreach ($this->tablesHasImageObject as $value) {
            $tableHasName = $value['tableName'];
            $tableHasPart = strstr($value['tableName'], "_", true);
            $query = "select * from $tableHasName, $tableHasPart WHERE idimageObject=$idIsPartOf AND $tableHasPart.id$tableHasPart=$tableHasName.id$tableHasPart;";
            $data = PDOConnect::run($query);
            if (count($data) > 0) {
                foreach ($data as $valueTableIspartOf) {
                    $id = $valueTableIspartOf["id$tableHasPart"];
                    $info[] = [ "tableHasPart" => $tableHasPart, "idHasPart" => $id, "values" => $valueTableIspartOf  ];
                }
            }
        }
        return $info;
    }

    public static function listLocation($directory) {
        self::$LIST_LOCATIONS = self::$LIST_LOCATIONS ?? FileSystem::listDirectories($directory);
        return self::$LIST_LOCATIONS;
    }

    public static function listKeywords(): array {
        self::$KEYWORDS_LIST = self::$KEYWORDS_LIST ?? Api::get("ImageObject", [ "groupBy" => "keywords", "orderBy" => "keywords" ]);
        if(self::$KEYWORDS === null) {
            foreach (self::$KEYWORDS_LIST as $value) {
                self::$KEYWORDS[] = $value['keywords'];
            }
        }
        return self::$KEYWORDS;
    }
}
