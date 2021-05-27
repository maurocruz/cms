<?php
namespace Plinct\Cms\Server;

class SchemaorgData {
    private static $SCHEMAORG_DATA;
    private static $SCHEMAORG_DATA_GRAPH;
    private static $SCHEMAORG_DATA_CLASS;
    private static $TYPE_SELECTED;
    private static $SEPARATOR;
    private static $INCLUDE_TYPE;
    private $response = [];

    public function __construct() {
        self::$SCHEMAORG_DATA = json_decode(file_get_contents(__DIR__.'/../../static/json/schemaorg.jsonld'), true);
        self::$SCHEMAORG_DATA_GRAPH = self::$SCHEMAORG_DATA['@graph'];
    }

    public static function searchById($id): array {
        foreach (self::$SCHEMAORG_DATA_GRAPH as $value ) {
            if ($value['@id'] == $id) return $value;
        }
        return [];
    }

    public static function searchBySubClassOf($id): array {
        $response = [];
        foreach (self::$SCHEMAORG_DATA_CLASS as $value ) {
            $subClassOf = $value['rdfs:subClassOf'] ?? null;
            if (isset($subClassOf['@id'])) {
                if ($subClassOf['@id'] == $id) {
                    $response[] = $value;
                }
            } elseif (is_array($subClassOf)) {
                foreach ($subClassOf as $valueSubClass) {
                    if ($valueSubClass['@id'] == $id) {
                        $response[] = $value;
                    }
                }
            }
        }
        return $response;
    }

    public function getSchemaByTypeSelected($typeSelected, bool $includeType = true, string $separator = " -> "): array {
        self::$TYPE_SELECTED = $typeSelected;
        self::$INCLUDE_TYPE = $includeType;
        self::$SEPARATOR = $separator;
        // FILTER ONLY TYPE IS CLASS
        self::filterOnlyTypeClass();
        // SELECT BY TYPE
        $this->selectByType();
        // exclude "schema:" from string responses
        return $this->extractSchemaType($this->response);
    }

    private static function filterOnlyTypeClass() {
        $response = null;
        foreach (self::$SCHEMAORG_DATA_GRAPH as $value) {
            if ($value['@type'] == 'rdfs:Class') {
                $response[] = $value;
            }
        }
        self::$SCHEMAORG_DATA_CLASS = $response;
    }

    private function selectByType() {
        foreach (self::$SCHEMAORG_DATA_CLASS as $value) {
            if (self::$TYPE_SELECTED == str_replace("schema:", "", $value['@id'])) {
                $this->response[] = $value['@id'];
                $this->selectSubClassOf($value['@id']);
            }
        }
    }

    private function selectSubClassOf($class) {
        foreach (self::searchBySubClassOf($class) as $valueSubClassOf) {
            if ($this->endItem() == $class) {
                $class = end($this->response);
            }
            $this->response[] = $class . self::$SEPARATOR . $valueSubClassOf['@id'];
            if (!empty(self::searchBySubClassOf($valueSubClassOf['@id']))) {
                $this->selectSubClassOf($valueSubClassOf['@id']);
            }
        }
    }

    private function endItem() {
        $response = substr(strrchr(end($this->response), self::$SEPARATOR),1);
        return $response === false ? end($this->response) : $response;
    }

    private function extractSchemaType(array $array): array {
        $response = [];
        foreach ($array as $value) {
            $newValue = $this->excludeType($value);
            if ($newValue) {
                $response[] = str_replace("schema:", "", $newValue);
            }
        }
        return $response;
    }

    private function excludeType(string $string): ?string {
        if (self::$INCLUDE_TYPE === false) {
            if ($string == "schema:" . self::$TYPE_SELECTED) return null;
            return str_replace(self::$TYPE_SELECTED.self::$SEPARATOR,"",$string);
        }
        return $string;
    }
}