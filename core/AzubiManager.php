<?php
namespace Core;

class AzubiManager {

    public $Azubi;
    public $StandardplanPhasen;

    public $PraeferierteAbteilungen = [];
    public $PraeferierteOptionaleAbteilungen = [];
    public $OptionaleAbteilungen = [];
    public $UnmarkedAbteilungen = [];

    public $Plan = [];

    public function __construct($azubi, $standardplan) {
        $this->Azubi = $azubi;
        $this->StandardplanPhasen = $standardplan->Phasen;

        $this->SortAbteilungen();
    }

    public function AddToPlan($anfrage) {
        $this->Plan[] = [
            "ID_Abteilung" => $anfrage["ID_Abteilung"],
            "PeriodTime" => $anfrage["StartDate"] . " " . $anfrage["EndDate"]
        ];

        $this->RemoveAbteilungFromRemaining($anfrage["ID_Abteilung"]);
    }

                                                // in Wochen
    public function CreateAnfrage($id_abteilung, $dauer) {

        $anfrage = [
            "ID_Abteilung" => $id_abteilung,
            "StartDate" => "",
            "EndDate" => ""
        ];

        $datesOfTimePeriod = $this->FindNextFreePeriodInPlan();
        $anfrage["StartDate"] = $datesOfTimePeriod["StartDate"];
        $possibleEndDate = $datesOfTimePeriod["EndDate"];
        $dateAfterDauer = date("Y-m-d", strtotime(
            "+" . strval($dauer) . " week",
            strtotime($anfrage["StartDate"])
        ));

        if ($dateAfterDauer < $possibleEndDate) {
            $possibleEndDate = $dateAfterDauer;
        }

        $anfrage["EndDate"] = $possibleEndDate;

        return $anfrage;
    }

    private function FindNextFreePeriodInPlan() {

        if (empty($this->Plan)) {
            return ["StartDate" => $this->Azubi->Ausbildungsstart, "EndDate" => $this->Azubi->Ausbildungsende];
        }

        for ($i = 0; $i < count($this->Plan); $i++) {

            $datesOfTimePeriod = $this->SplitTimePeriodDates($this->Plan[$i]["PeriodTime"]);
            $dateAfterEndDate = date("Y-m-d", strtotime($datesOfTimePeriod["EndDate"] . "+1 day"));

            if ($i + 1 >= count($this->Plan)) {
                return [
                    "StartDate" => $dateAfterEndDate,
                    "EndDate" => $this->Azubi->Ausbildungsende
                ];
            }

            $datesOfNextTimePeriod = $this->SplitTimePeriodDates($this->Plan[$i + 1]["PeriodTime"]);

            if ($dateAfterEndDate !== $datesOfNextTimePeriod["StartDate"]) {
                return [
                    "StartDate" => $dateAfterEndDate,
                    "EndDate" => $datesOfNextTimePeriod["StartDate"]
                ];
            }
        }
    }

    private function RemoveAbteilungFromRemaining($id_abteilung) {

        if (array_key_exists($id_abteilung, $this->PraeferierteAbteilungen)) {
            unset($this->PraeferierteAbteilungen[$id_abteilung]);
        } elseif (array_key_exists($id_abteilung, $this->PraeferierteOptionaleAbteilungen)) {
            unset($this->PraeferierteOptionaleAbteilungen[$id_abteilung]);
        } elseif (array_key_exists($id_abteilung, $this->UnmarkedAbteilungen)) {
            unset($this->UnmarkedAbteilungen[$id_abteilung]);
        } elseif (array_key_exists($id_abteilung, $this->OptionaleAbteilungen)) {
            unset($this->OptionaleAbteilungen[$id_abteilung]);
        }
    }

    private function SortAbteilungen() {

        foreach ($this->StandardplanPhasen as $phase) {

            if ($phase->Praeferieren && !$phase->Optional) {
                $this->PraeferierteAbteilungen[$phase->ID_Abteilung] = $phase;
            } elseif ($phase->Praeferieren && $phase->Optional) {
                $this->PraeferierteOptionaleAbteilungen[$phase->ID_Abteilung] = $phase;
            } elseif (!$phase->Praeferieren && $phase->Optional) {
                $this->OptionaleAbteilungen[$phase->ID_Abteilung] = $phase;
            } else {
                $this->UnmarkedAbteilungen[$phase->ID_Abteilung] = $phase;
            }
        }
    }

    private function SplitTimePeriodDates($timePeriod) {
        $dates = explode(" ", $timePeriod);
        return [
            "StartDate" => $dates[0],
            "EndDate"   => $dates[1]
        ];
    }
}
