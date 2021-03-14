<?php

namespace Plinct\Web;

class Render
{
    public function convertObjectToString($object): string
    {
        return self::writeObject($object);
    }
    
    private static function writeObject($object): string
    {
        // open tag
        $response = '<'.$object->getTagname();
        
        // attributes
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $name => $value) {
                if (is_int($name)) {
                    $response .= " $value";
                } else {
                    $response .= ' '.$name.'="'.$value.'"';
                }
            }
        }
        $response .= '>';
        
        //content        
        if (is_array($object->getChildren())) {
            foreach ($object->getChildren() as $value) {
                if (is_string($value)) {
                    $response .= $value;
                    
                } elseif (is_object($value)) {
                    $response .= self::writeObject($value);
                    
                } elseif (is_array($value)) {
                    $response .= self::tag($value);                    
                }                
            }            
        }
        
        if (is_object($object->getChildren())) {
            $response .= self::writeObject($object->getChildren());
        }
        
        // close tag
        $response .= in_array($object->getTagname(), ["meta","link","img","input"]) ? null : '</'.$object->getTagname().'>'; 
        
        return $response;
    }
    
    public static function arrayToString(array $array = null): string
    {         
        $response = null;
        
        if (array_key_exists("tag", $array)) {
            $response .= self::tag($array);
            
        } elseif (array_key_exists("object", $array) || array_key_exists("obj", $array)) {
            $object = $array["object"] ?? $array["obj"];
            
            $classname = "Plinct\\Web\\Object\\" . ucfirst($object)."Object";
            $response .= self::tag((new $classname)($array));            
            
        } elseif (array_key_exists("include", $array)) {
            $response .= self::tag(json_decode(file_get_contents($array['include']), true));
            
        } elseif (is_array($array)) {        
            foreach ($array as $value) {
                $response .= is_array($value) ? self::arrayToString($value) : $value;
            }
        }        
        return $response ?? "[ empty! ]";
    }
    
    private static function tag($value): string
    {    
            // open
            $response = "<".$value['tag'];
            
            // attributes
            $response .= self::attributes($value['attributes'] ?? null);
            
            //close
            $response .= ">";                        
            
            //content            
            if (array_key_exists("content", $value)) {
                if (isset($value['href'])) {
                    $response .= "<a href=\"{$value['href']}\"";
                    $response .= self::attributes($value['hrefAttributes'] ?? null);                    
                    $response .= ">";                     
                    // content
                    $response .= is_array($value['content']) ? self::arrayToString($value['content']) : $value['content'];                    
                    $response .= "</a>";
                    
                } else {                    
                    // content
                    $response .= is_array($value['content']) ? self::arrayToString($value['content']) : $value['content'];
                }
            }
            
            $noend = [ "meta", "link", "img", "input", "source" ];
            
            $response .= in_array($value['tag'], $noend) ? null : "</".$value['tag'].">";
            
            return $response;
    }
    
    private static function attributes(array $array = null) 
    {
        $response = null;        
        if ($array) {
            foreach ($array as $key => $value) 
            {
                $response .= is_int($key) ? " $value" : " $key=\"$value\"";
            }
        }        
        return $response;
    }
}
