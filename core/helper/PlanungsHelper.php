<?php
/**
 * PlanungsHelper.php
 *
 * Enthält die Klasse PlanungsHelper.php, mit welcher eine automatische
 * Planung vereinfacht wird.
 */

namespace core\helper;

use core\helper\DataHelper;
use core\helper\DateHelper;
use models\Plan;

if (!defined('BASE')) {
    include_once(BASE . '/config.php');
} else {
    include_once(dirname(dirname(__DIR__)) . '/config.php');
}

/**
 * Helper-Klasse für die automatische Planung.
 */
class PlanungsHelper {

    /**
     * @var Azubi Der aktuelle Azubi.
     */
    public $Azubi;

    /**
     * @var DataHelper Eine Instanz der DataHelper-Klasse zum Beschaffen von Daten.
     */
    public $Helper;

    /**
     * @var Plan[] Die erstellten Pläne.
     */
    public $Plaene = [];

    /**
     * @var array Eine Liste von Phasen des Standardplans, die noch nicht verplant sind.
     */
    public $AbteilungenLeft = [];

    /**
     * @var array Eine Liste von belegten in den einzelnen Abteilungen.
     */
    private $BelegteZeitraeumeInAbteilungen = [];

    /**
     * Vorbereitungen
     *
     * @param Azubi $azubi Der Azubi, für den die Pläne erstellt werden sollen.
     */
    public function __construct($azubi) {
        $this->Azubi = $azubi;
        $this->Helper = new DataHelper();
    }

    /**
     * Unternimmt drei Versuche die gegebenen Abteilungen zu verplanen. Wenn
     * eine Abteilung innerhalb dieser drei Versuche nicht verplant werden
     * konnte, wird die Abteilung in die Liste $this->AbteilungenLeft
     * eingetragen.
     *
     * @param array $abteilungen Die zu verplanenden Abteilungen.
     */
    public function PlanAbteilungen($abteilungen) {

        $this->AbteilungenLeft = $this->AbteilungenLeft + $abteilungen;

        // drei Mal versuchen einen Zeitraum in jeder Abteilung zu finden
        for ($i = 0; $i < 3; $i++) {

            $abteilungen = $this->ShuffleAbteilungen($abteilungen);

            foreach ($abteilungen as $abteilung) {

                if (array_key_exists($abteilung->ID_Abteilung, $this->AbteilungenLeft)) {

                    $lastPlanEndDate = end($this->Plaene)->Enddatum;
                    $startDate = DateHelper::DayAfter($lastPlanEndDate);
                    $zeitraeume = $this->CreateZeitraeume($startDate, $abteilung->Wochen);
                    $alleWochenFrei = true;

                    foreach ($zeitraeume as $zeitraum) {

                        if (!$this->IsZeitraumInAbteilungFrei(
                            DateHelper::BuildTimePeriodString($zeitraum['Startdatum'], $zeitraum['Enddatum']),
                            $abteilung->ID_Abteilung
                        )) {
                            $alleWochenFrei = false;
                            break;
                        }
                    }

                    if ($alleWochenFrei) {

                        $ansprechpartner = $this->GetAnsprechpartnerFuerAbteilung($abteilung->ID_Abteilung);

                        foreach ($zeitraeume as $zeitraum) {
                            $this->CreatePlanPhase($abteilung->ID_Abteilung, $zeitraum['Startdatum'], $zeitraum['Enddatum'], $ansprechpartner);
                        }

                        unset($this->AbteilungenLeft[$abteilung->ID_Abteilung]);
                    }
                }
            }
        }

        $this->SortPlaene();
    }

    /**
     * Verplant die Abteilungen, die bisher nicht verplant werden konnten. Dabei
     * wird keine Rücksicht darauf genommen, ob in einem Zeitraum eine Abteilung
     * bereits die maximale Anzahl an Azubis ausbildet.
     */
    public function PlanLeftAbteilungen() {

        $this->AbteilungenLeft = $this->ShuffleAbteilungen($this->AbteilungenLeft);

        foreach ($this->AbteilungenLeft as $abteilung) {

            $lastPlanEndDate = end($this->Plaene)->Enddatum;
            $startDate = DateHelper::DayAfter($lastPlanEndDate);
            $zeitraeume = $this->CreateZeitraeume($startDate, $abteilung->Wochen);

            if (!empty($zeitraeume)) {

                $ansprechpartner = $this->GetAnsprechpartnerFuerAbteilung($abteilung->ID_Abteilung);

                foreach ($zeitraeume as $zeitraum) {
                    $this->CreatePlanPhase($abteilung->ID_Abteilung, $zeitraum['Startdatum'], $zeitraum['Enddatum'], $ansprechpartner);
                }

                unset($this->AbteilungenLeft[$abteilung->ID_Abteilung]);
            }
        }

        $this->SortPlaene();
    }

    /**
     * Verplant den ersten Aufenthalt des Azubis in einer der gegebenen
     * Abteilungen.
     * Wenn anfangs kein Zeitraum gefunden werden konnte, wird ein Plan in einer
     * zufälligen der gegebenden Abteilungen erstellt, ohne dabei zu prüfen, ob
     * die Abteilung in diesem Zeitraum bereits die maximale Anzahle an Azubis
     * ausbildet.
     * Sobald ein Plan in einem Zeitraum erstellt wurde, werden alle weiteren
     * gegebenen Abteilungen in die Liste $this->AbteilungenLeft aufgenommen.
     *
     * @param array $abteilungen Die Abteilungen, von denen eine verplant werden
     *                           soll.
     */
    public function PlanStartOfAusbildung($abteilungen) {

        $this->AbteilungenLeft = $abteilungen;
        $abteilungen = $this->ShuffleAbteilungen($abteilungen);

        $eingetragen = false;
        foreach ($abteilungen as $abteilung) {

            $ausbildungsStart = (DateHelper::IsMonday($this->Azubi->Ausbildungsstart))
                ? $this->Azubi->Ausbildungsstart
                : DateHelper::LastMonday($this->Azubi->Ausbildungsstart);

            $zeitraeume = $this->CreateZeitraeume($ausbildungsStart, $abteilung->Wochen);
            $alleWochenFrei = true;

            foreach ($zeitraeume as $zeitraum) {

                if (!$this->IsZeitraumInAbteilungFrei(
                    DateHelper::BuildTimePeriodString($zeitraum['Startdatum'], $zeitraum['Enddatum']),
                    $abteilung->ID_Abteilung
                )) {
                    $alleWochenFrei = false;
                    break;
                }
            }

            if ($alleWochenFrei) {

                $eingetragen = true;
                $ansprechpartner = $this->GetAnsprechpartnerFuerAbteilung($abteilung->ID_Abteilung);
                unset($this->AbteilungenLeft[$abteilung->ID_Abteilung]);

                foreach ($zeitraeume as $zeitraum) {
                    $this->CreatePlanPhase($abteilung->ID_Abteilung, $zeitraum['Startdatum'], $zeitraum['Enddatum'], $ansprechpartner);
                }

                break;
            }
        }

        // Da keine Abteilung frei ist -> maximale Anzahl einer zufälligen präferierten Abteilung ignorieren
        if (!$eingetragen) {

            $randomAbteilung = $abteilungen[array_rand($abteilungen)];
            unset($this->AbteilungenLeft[$randomAbteilung->ID_Abteilung]);

            $startDate = (DateHelper::IsMonday($this->Azubi->Ausbildungsstart))
                ? $this->Azubi->Ausbildungsstart
                : DateHelper::LastMonday($this->Azubi->Ausbildungsstart);
            $endDate = DateHelper::NextSunday($startDate);

            $ansprechpartner = $this->GetAnsprechpartnerFuerAbteilung($randomAbteilung->ID_Abteilung);

            for ($i = 0; $i <= $randomAbteilung->Wochen; $i++) {
                $this->CreatePlanPhase($randomAbteilung->ID_Abteilung, $startDate, $endDate, $ansprechpartner);
                $startDate = DateHelper::NextMonday($startDate);
                $endDate = DateHelper::NextSunday($endDate);
            }
        }

        $this->SortPlaene();
    }

    /**
     * Erstellt die einzelnen Phasen vom Startdatum ausgehend für die nächsten
     * einzelnen Wochen.
     *
     * @param string    $startDate      Das Startdatum der ersten Phase.
     * @param int       $anzahlWochen   Die Anzahl der zu erstellenden Phasen.
     *
     * @return array Die einzelnen Zeiträume mit Start- und Enddatum.
     *               Aufbau Array:
     *               [0] => [
     *                   'Startdatum' => '[STARTDATUM Y-m-d]',
     *                   'Enddatum'   => '[ENDDATUM Y-m-d]'
     *               ],
     *               [1] => ...
     */
    private function CreateZeitraeume($startDate, $anzahlWochen) {

        $endDate = DateHelper::NextSunday($startDate);

        $zeitraeume = [];
        for ($i = 0; $i < $anzahlWochen; $i++) {

            if ($startDate > $this->Azubi->Ausbildungsende) break;

            $zeitraeume[] = [ 'Startdatum' => $startDate, 'Enddatum' => $endDate];
            $startDate = DateHelper::NextMonday($startDate);
            $endDate = DateHelper::NextSunday($endDate);
        }

        return $zeitraeume;
    }

    /**
     * Erstellt eine neue Instanz des Models "Plan" und fügt diese den Plänen
     * in $this->Plaene hinzu.
     *
     * @param Phase             $id_abteilung       Die ID der Abteilung, zu der
     *                                              der Plan erstellt werden
     *                                              soll.
     * @param string            $startDate          Das Startdatum des Plans.
     * @param string            $endDate            Das Enddatum des Plans.
     * @param Ansprechpartner   $ansprechpartner    Der Ansprechpartner für den
     *                                              zu erstellenden Plan.
     */
    private function CreatePlanPhase($id_abteilung, $startDate, $endDate, $ansprechpartner) {

        $this->Plaene[] = new Plan(
            $this->Azubi->ID,
            (empty($ansprechpartner)) ? null : $ansprechpartner->ID,
            $id_abteilung,
            $startDate,
            $endDate,
            ''
        );
    }

    /**
     * Ermittelt einen zufälligen Ansprechpartner, der für die angeforderte
     * Abteilung eingetragen ist.
     *
     * @param int $id_abteilung Die ID der Abteilung, für die der
     *                          Ansprechpartner
     *                          eingetragen sein soll.
     *
     * @return Ansprechpartner Ein zufällig ausgewählter Ansprechpartner, der
     *                         für die angeforderte Abteilung eingetragen ist.
     */
    private function GetAnsprechpartnerFuerAbteilung($id_abteilung) {

        $abteilungsAnsprechpartner = [];
        foreach ($this->Helper->GetAnsprechpartner() as $ansprechpartner) {

            if ($ansprechpartner->ID_Abteilung === $id_abteilung) {
                $abteilungsAnsprechpartner[] = $ansprechpartner;
            }
        }

        return (empty($abteilungsAnsprechpartner))
            ? false
            : $abteilungsAnsprechpartner[mt_rand(0, count($abteilungsAnsprechpartner) - 1)];;
    }

    /**
     * Ermittelt die belegten Zeiträume einer Abteilung.
     * Ein Zeitraum gilt als belegt, sobald ein Azubi für diesen Zeitraum
     * geplant ist. Es heißt nicht, dass die maximale Anzahl an Azubis für die
     * Abteilung innerhalb dieses Zeitraums bereits erreicht ist.
     *
     * @param int $id_abteilung Die ID der Abteilung, zu der die belegten
     *                          Zeiträume ermittelt werden sollen.
     *
     * @return array Die belegten Zeiträume.
     *               Aufbau des Arrays:
     *               [
     *                   (Startdatum_1 Enddatum_2) => Anzahl an Azubis,
     *                   ...
     *                   (Startdatum_n Enddatum_n) => Anzahl an Azubis
     *               ]
     */
    private function GetBelegteZeitraeumeInAbteilungen($id_abteilung) {

        $abteilungsPlaene = [];
        $belegteZeitraeume = []; // ... in Abteilungen

        foreach ($this->Helper->GetPlaene() as $plan) { // TODO: Pläne nur einmal während kompletter Ausführung holen
            if ($plan === $id_abteilung) {
                $abteilungsPlaene[] = $plan;
            }
        }

        if (!empty($abteilungsPlaene)) {

            foreach ($abteilungsPlaene as $plan) {

                $startDate = $plan->Startdatum;
                $endDate = $plan->Enddatum;
                $timePeriodString = DateHelper::BuildTimePeriodString($startDate, $endDate);

                if (empty($belegteZeitraeume)) {
                    $belegteZeitraeume[$timePeriodString] = 1;
                    continue;
                }

                $eingetragen = false;
                foreach ($belegteZeitraeume as $zeitraum => $anzahlAzubis) {

                    if ($timePeriodString === $zeitraum) {
                        $belegteZeitraeume[$zeitraum]++;
                        $eingetragen = true;
                    }
                }

                if (!$eingetragen) {
                    $belegteZeitraeume[$timePeriodString] = 1;
                }
            }
        }

        ksort($belegteZeitraeume);
        return $belegteZeitraeume;
    }

    /**
     * Untersucht, ob für den gegebenen Zeitraum die Abteilung die maximale
     * Anzahl an Azubis noch nicht erreicht hat, sprich ob sie noch weitere
     * Azubis ausbilden kann.
     *
     * @param string    $timePeriod     Der Zeitraum im Format "Y-m-d Y-m-d"
     *                                  (Startdatum[Leerzeichen]Enddatum).
     * @param int       $id_abteilung   Die ID der Abteilung, für die der
     *                                  Zeitraum überprüft werden soll.
     *
     * @return bool Der Status, ob die Abteilung noch weitere Azubis ausbilden
     *              kann.
     */
    private function IsZeitraumInAbteilungFrei($timePeriod, $id_abteilung) {

        $abteilung = $this->Helper->GetAbteilungen($id_abteilung);

        if (!array_key_exists($id_abteilung, $this->BelegteZeitraeumeInAbteilungen)) {
            $this->BelegteZeitraeumeInAbteilungen[$id_abteilung] = $this->GetBelegteZeitraeumeInAbteilungen($id_abteilung);
        }

        if (array_key_exists($timePeriod, $this->BelegteZeitraeumeInAbteilungen[$id_abteilung])) {

            if ($this->BelegteZeitraeumeInAbteilungen[$id_abteilung][$timePeriod] >= $abteilung->MaxAzubis) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mischt die Abteilungen anhand der Keys (Keys = IDs der Abteilungen).
     *
     * @param array $abteilungen Die zu mischenden Abteilungen.
     *
     * @return array Die gemischten Abteilungen.
     */
    private function ShuffleAbteilungen($abteilungen) {

        $keys = array_keys($abteilungen);
        shuffle($keys);
        $shuffledAbteilungen = [];

        foreach($keys as $id_abteilung) {
            $shuffledAbteilungen[$id_abteilung] = $abteilungen[$id_abteilung];
        }

        return $shuffledAbteilungen;
    }

    /**
     * Sortiert die Pläne nach den Startdaten.
     */
    private function SortPlaene() {

        /**
         * @see https://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
         */
        usort($this->Plaene, function ($a, $b) {
            return strcmp($a->Startdatum, $b->Startdatum);
        });
    }
}
