<?php
namespace Core\Helper;

class DateHelper {

    private const defaultFormat = "Y-m-d";

    public static function BuildTimePeriodString($startDate, $endDate, $limiter = " ") {
        return $startDate . $limiter . $endDate;
    }

    public static function DayAfter($date) {
        return date(self::defaultFormat, strtotime("$date +1 day"));
    }

    public static function DayBefore($date) {
        return date(self::defaultFormat, strtotime("$date -1 day"));
    }

    public static function FormatDate($date, $format = "d.m.Y") {
        return date($format, strtotime($date));
    }

    public static function GetDatesFromString($date, $delimiter = " ") {
        $dates = explode($delimiter, $date);
        return [
            "StartDatum"    => $dates[0],
            "EndDatum"      => $dates[1]
        ];
    }

    public static function InRange($date, $startDate, $endDate) {
        if ($date >= $startDate && $date <= $endDate ) return true;
        return false;
    }

    public static function IsMonday($date) {
        return strtolower(date("l", strtotime($date))) === "monday";
    }

    public static function LastMonday($date) {
        return date("Y-m-d", strtotime($date . "last monday"));
    }

    public static function NextMonday($date, $format = self::defaultFormat) {
        return date($format, strtotime($date . " next monday"));
    }

    public static function NextSunday($date, $format = self::defaultFormat) {
        return date($format, strtotime($date . " next sunday"));
    }
}
