<?php

namespace Plinct\Cms\Server;

use Plinct\Api\Server\PDOConnect;
use Plinct\Cms\App;

class ImageObjectServer
{
    private $tablesHasImageObject;

    public function __construct()
    {
        $table_schema = PDOConnect::getDbname();
        $this->tablesHasImageObject = PDOConnect::run("select table_name from information_schema.tables WHERE table_schema = '$table_schema' AND  table_name LIKE '%_has_imageObject';");
    }


    public function getImageHasPartOf($idIsPartOf)
    {
        $info = null;

        foreach ($this->tablesHasImageObject as $value) {
            $tableHasName = $value['TABLE_NAME'];
            $tableHasPart = strstr($value['TABLE_NAME'], "_", true);

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
}