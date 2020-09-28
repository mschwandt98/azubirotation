<?php
namespace Core\Helper;

class DateHelper {

    public static function FormatDate($date) {
        return date("d.m.Y", strtotime($date));
    }
}
