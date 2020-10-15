<?php
/**
 * DateHelper.php
 *
 * Enthält die statische Klasse DateHelper, welche Funktionen zum Umgang mit
 * Daten bereitstellt.
 *
 * TODO: Diesen Helper gegen eine Extension wie bspw Carbon ersetzen.
 * https://github.com/briannesbitt/Carbon
 */

namespace core\helper;

/**
 * Helper-Klasse für einen einheitlichen Umgang mit Daten.
 */
class DateHelper {

    /**
     * @var string Das Standard-Format für Daten.
     */
    private const defaultFormat = "Y-m-d";

    /**
     * Erstellt einen String aus zwei Daten mit einen Limiter dazwischen.
     *
     * @param string $startDate Das Startdatum.
     * @param string $endDate   Das Enddatum.
     * @param string $limiter   Der Limiter zwischen den Daten. Standard: " ".
     *
     * @return string Ein String mit dem zusammengesetzten Start- und Enddatum.
     */
    public static function BuildTimePeriodString($startDate, $endDate, $limiter = " ") {
        return $startDate . $limiter . $endDate;
    }

    /**
     * Gibt das Datum des nächsten Tages zurück (Y-m-d).
     *
     * @param string $date Das Datum, zu dem der folgende Tag benötigt wird.
     *
     * @return string Das Datum des Tages nach dem hereingegebenden Datum.
     */
    public static function DayAfter($date) {
        return date(self::defaultFormat, strtotime("$date +1 day"));
    }

    /**
     * Gibt das Datum des vorherigen Tages zurück (Y-m-d).
     *
     * @param string $date Das Datum, zu dem der vorherige Tag benötigt wird.
     *
     * @return string Das Datum des Tages vor dem hereingegebenden Datum.
     */
    public static function DayBefore($date) {
        return date(self::defaultFormat, strtotime("$date -1 day"));
    }

    /**
     * Formatiert ein Datum.
     *
     * @param string $date      Das zu formatierende Datum.
     * @param string $format    Das gewünschte Format. Standard: d.m.Y
     *
     * @return string Das formatierte Datum.
     */
    public static function FormatDate($date, $format = "d.m.Y") {
        return date($format, strtotime($date));
    }

    /**
     * Gibt das Datum in der gewünschten Anzahl an Wochen zurück.
     *
     * @param string    $date   Das Ausgangsdatum.
     * @param int       $weeks  Die Anzahl der Wochen.
     * @param string    $format Das Format des Rückgabedatums. Standard: Y-m-d
     *
     * @return string Das Datum in der gewünschten Anzahl an Wochen.
     */
    public static function GetDateInXWeeks($date, $weeks, $format = self::defaultFormat) {
        return date($format, strtotime("+$weeks weeks", strtotime($date)));
    }

    /**
     * Holt zwei Daten aus einem String.
     *
     * @param string $dateString    Der String mit den Daten.
     * @param string $delimiter     Der Delimiter für den String.
     *
     * @return string[] Das "StartDatum" und "EndDatum" des gegebenden Strings.
     */
    public static function GetDatesFromString($dateString, $delimiter = " ") {
        $dates = explode($delimiter, $dateString);
        return [
            "StartDatum"    => $dates[0],
            "EndDatum"      => $dates[1]
        ];
    }

    /**
     * Prüft, ob ein Datum im Zeitraum zwischen zwei gegebenden Daten liegt.
     *
     * @param string $date      Das zu prüfende Datum. Format: Y-m-d
     * @param string $startDate Das erste Datum des Zeitraums. Format: Y-m-d
     * @param string $endDate   Das letzte Datum des Zeitraums. Format: Y-m-d
     *
     * @return bool Der Status, ob das gegebende Datum im Zeitraum liegt.
     */
    public static function InRange($date, $startDate, $endDate) {
        if ($date >= $startDate && $date <= $endDate ) return true;
        return false;
    }

    /**
     * Prüft, ob das gegebende Datum auf einen Montag fällt.
     *
     * @param string $date Das zu prüfende Datum.
     *
     * @return bool Der Status, ob das gegebende Datum auf einen Montag fällt.
     */
    public static function IsMonday($date) {
        return strtolower(date("l", strtotime($date))) === "monday";
    }

    /**
     * Ermittelt das Datum vom letzten Montag.
     *
     * @param string $date Das Datum, von dem aus das Datum des letzen Montags
     *                     ermittelt werden soll.
     *
     * @param string Das Datum des letzten Montags.
     */
    public static function LastMonday($date) {
        return date("Y-m-d", strtotime($date . "last monday"));
    }

    /**
     * Ermittelt das Datum vom nächsten Montag.
     *
     * @param string $date Das Datum, von dem aus das Datum des nächsten Montags
     *                     ermittelt werden soll.
     *
     * @param string Das Datum des nächsten Montags.
     */
    public static function NextMonday($date, $format = self::defaultFormat) {
        return date($format, strtotime($date . " next monday"));
    }

    /**
     * Ermittelt das Datum vom nächsten Sonntag.
     *
     * @param string $date Das Datum, von dem aus das Datum des nächsten
     *                     Sonntags ermittelt werden soll.
     *
     * @param string Das Datum des nächsten Sonntags.
     */
    public static function NextSunday($date, $format = self::defaultFormat) {
        return date($format, strtotime($date . " next sunday"));
    }
}
