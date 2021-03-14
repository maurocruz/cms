<?php
namespace Plinct\Cms\Server;

use Plinct\PDO\PDOConnect;
use Plinct\Api\Type\ImageObject;

class ImageObjectServer {
    private $tablesHasImageObject;
    private static $KEYWORDS_LIST;
    private static $KEYWORDS;
    private static $LIST_LOCATIONS;

    public function __construct() {
        $table_schema = PDOConnect::getDbname();
        $this->tablesHasImageObject = PDOConnect::run("select table_name as tableName from information_schema.tables WHERE table_schema = '$table_schema' AND table_name LIKE '%_has_imageObject';");
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
        self::$LIST_LOCATIONS = self::$LIST_LOCATIONS ?? self::listDirectories($directory);
        return self::$LIST_LOCATIONS;
    }

    private static function listDirectories($directory, $arrayReturn = null) {
        foreach (scandir($directory) as $key => $value) {
            if (!in_array($value,array(".","..","thumbs"))) {
                $path = $directory . DIRECTORY_SEPARATOR . $value;
                if (is_dir($directory . DIRECTORY_SEPARATOR . $value)) {
                    $arrayReturn[] = str_replace($_SERVER['DOCUMENT_ROOT'], "",$path);
                    $arrayReturn = self::listDirectories($path, $arrayReturn);
                }
            }
        }
        return $arrayReturn;
    }

    public static function listKeywords(): array {
        self::$KEYWORDS_LIST = self::$KEYWORDS_LIST ?? (new ImageObject())->get([ "groupBy" => "keywords", "orderBy" => "keywords" ]);
        if(self::$KEYWORDS === null) {
            foreach (self::$KEYWORDS_LIST as $value) {
                self::$KEYWORDS[] = $value['keywords'];
            }
        }
        return self::$KEYWORDS;
    }
}
