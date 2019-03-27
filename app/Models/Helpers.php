<?php

namespace App\Models;

class Helpers {

    public function renamePrefixKeyArray($inputArray, $prefix) {
        $keys = array_keys($inputArray);
        array_walk($keys, function (&$value, $omit, $prefix) {
            $value = str_replace($prefix, '', $value);
        }, $prefix);
        $newArray = array_combine($keys, $inputArray);
        return $newArray;
    }

    public function ajustArrayPaginate($array) {
        $prefix = "";
        $newArray = $this->renamePrefixKeyArray($array, $prefix);
        unset($newArray["items"]);
        unset($newArray["path"]);
        unset($newArray["pageName"]);
        unset($newArray["query"]);
        unset($newArray["fragment"]);
        return $newArray;
    }

    public static function isNullOrEmpty($value){
        if($value != null && isset($value) && !empty($value)){
            return false;
        }
        return true;
    }

    public static function rand_passwd($length = 6) {
        $chars = '@-abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

}
