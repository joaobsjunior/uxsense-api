<?php

namespace App\Models;

class Validation {

    public static function isTime($time) {
        $options = array("options" => array("regexp" => "/(\d{2}|\d{1})\:\d{2}$/"));
        $filter = filter_var($time, FILTER_VALIDATE_REGEXP, $options);
        if ($filter) {
            $diviser = strpos($time, ":");
            $hours = intval(substr($time, 0, $diviser));
            $minutes = intval(substr($time, $diviser + 1));
            if (max($hours, 23) == 23 && min($hours, 0) == 0 && max($minutes, 59) == 59 && min($hours, 0) == 0) {
                return true;
            }
        }
        return false;
    }

    public static function isEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function isDate($date) {
        $options = array("options" => array("regexp" => "/\d{4}\-\d{2}\-\d{2}$/"));
        $filter = filter_var($date, FILTER_VALIDATE_REGEXP, $options);
        if ($filter) {
            $divider = strpos($date, "-");
            $year = intval(substr($date, 0, $divider));
            $month = intval(substr($date, $divider + 1, 2));
            $divider = strrpos($date, "-");
            $day = intval(substr($date, $divider + 1, 2));
            if (checkdate($month, $day, $year)) {
                return true;
            }
            return false;
        }
    }

}
