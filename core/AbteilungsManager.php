<?php
namespace Core;

class AbteilungsManager {

    private $abteilung = [];
    private $plan = [];

    private $tempTimePeriods = [];

    public function __construct($abteilung, $planTimePeriod) {
        $this->abteilung = $abteilung;
        $this->plan[$planTimePeriod] = 0;
    }

    public function HandleAnfrage($anfrage) {

        $startDate = $anfrage["StartDate"];
        $endDate = $anfrage["EndDate"];

        if ($this->IsTimePeriodFree($startDate, $endDate)) {
            $this->AddToPlan($startDate, $endDate);
            return true;
        }

        return false;
    }

    private function AddToPlan($startDate, $endDate) {

        foreach ($this->temp as $tempTimePeriod) {

            $dates = $this->SplitTimePeriodDates($tempTimePeriod);

            // Anfang und Ende in Zeitspanne
            if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $startDate) &&
                $this->DateInRange($dates["StartDate"], $dates["EndDate"], $endDate)) {

                if ($dates["StartDate"] === $startDate && $dates["EndDate"] === $endDate) {
                    $this->plan[$tempTimePeriod] += 1;
                    break;
                }

                $firstPartOfPlan = [];
                $secondPartOfPlan = [];
                $thirdPartOfPlan = [];

                foreach ($this->plan as $timePeriod => $numberOfAzubis) {

                    if ($timePeriod != $tempTimePeriod && empty($secondPartOfPlan)) {
                        $firstPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }

                    if ($timePeriod === $tempTimePeriod) {

                        if ($dates["StartDate"] < $startDate && $dates["EndDate"] > $endDate) {
                            $secondPartOfPlan[$dates["StartDate"] . " " . $this->DateDayBefore($startDate)] = $numberOfAzubis;
                            $secondPartOfPlan["$startDate $endDate"] = $numberOfAzubis + 1;
                            $secondPartOfPlan[$this->DateDayAfter($endDate) . " " . $dates["EndDate"]] = $numberOfAzubis;
                        } elseif ($dates["StartDate"] < $startDate) {
                            $secondPartOfPlan[$dates["StartDate"] . " " . $this->DateDayBefore($startDate)] = $numberOfAzubis;
                            $secondPartOfPlan["$startDate $endDate"] = $numberOfAzubis + 1;
                        } elseif ($dates["EndDate"] > $endDate) {
                            $secondPartOfPlan["$startDate $endDate"] = $numberOfAzubis + 1;
                            $secondPartOfPlan[$this->DateDayAfter($endDate) . " " . $dates["EndDate"]] = $numberOfAzubis;
                        }

                        continue;
                    }

                    if ($timePeriod != $tempTimePeriod) {
                        $thirdPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }
                }

                $this->SetNewPlans($firstPartOfPlan, $secondPartOfPlan, $thirdPartOfPlan);
                continue;
            }

            if ($dates["StartDate"] === $startDate) {
                $this->plan[$tempTimePeriod]++;
                continue;
            }

            // Anfang in Zeitspanne
            if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $startDate)) {

                $firstPartOfPlan = [];
                $secondPartOfPlan = [];
                $thirdPartOfPlan = [];

                foreach ($this->plan as $timePeriod => $numberOfAzubis) {

                    if ($timePeriod != $tempTimePeriod && empty($secondPartOfPlan)) {
                        $firstPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }

                    if ($timePeriod === $tempTimePeriod) {
                        $secondPartOfPlan[$dates["StartDate"] . " " . $this->DateDayBefore($startDate)] = $numberOfAzubis;
                        $secondPartOfPlan["$startDate $endDate"] = $numberOfAzubis + 1;
                        continue;
                    }

                    if ($timePeriod != $tempTimePeriod) {
                        $thirdPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }
                }

                $this->SetNewPlans($firstPartOfPlan, $secondPartOfPlan, $thirdPartOfPlan);
                continue;
            }

            if ($dates["EndDate"] === $endDate) {
                $this->plan[$tempTimePeriod]++;
                continue;
            }

            // Zeitspanne zwischen Anfang und Ende
            /*if ($this->DateInRange($startDate, $endDate, $dates["StartDate"])) {
                $this->plan[$tempTimePeriod]++;
            }*/

            // Ende in Zeitspanne
            if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $endDate)) {

                $firstPartOfPlan = [];
                $secondPartOfPlan = [];
                $thirdPartOfPlan = [];

                foreach ($this->plan as $timePeriod => $numberOfAzubis) {

                    if ($timePeriod != $tempTimePeriod && empty($secondPartOfPlan)) {
                        $firstPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }

                    if ($timePeriod === $tempTimePeriod) {
                        $secondPartOfPlan[$dates["StartDate"] . " $endDate"] = $numberOfAzubis + 1;
                        $secondPartOfPlan[$this->DateDayAfter($endDate) . " " . $dates["EndDate"]] = $numberOfAzubis;
                        continue;
                    }

                    if ($timePeriod != $tempTimePeriod) {
                        $thirdPartOfPlan[$timePeriod] = $numberOfAzubis;
                        continue;
                    }
                }

                $this->SetNewPlans($firstPartOfPlan, $secondPartOfPlan, $thirdPartOfPlan);
                continue;
            }
        }
    }

    private function SetNewPlans($firstPart, $secondPart, $thirdPart) {

        $parts = [ $firstPart, $secondPart, $thirdPart ];
        $this->plan = [];

        foreach ($parts as $part) {

            foreach ($part as $timePeriod => $numberOfAzubis) {
                $this->plan[$timePeriod] = $numberOfAzubis;
            }
        }
    }

    private function DateInRange($startDate, $endDate, $date) {

        $ts = strtotime($startDate);
        $te = strtotime($endDate);
        $t = strtotime($date);

        return $t >= $ts && $t <= $te;
    }

    private function IsTimePeriodFree($startDate, $endDate) {

        $freeTimePeriodes = $this->GetFreeTimePeriodes();

        if (empty($freeTimePeriodes)) {
            return false;
        }

        $startDateFree = false;
        $endDateFree = false;

        foreach ($freeTimePeriodes as $timePeriod => $numberOfAzubis) {

            $dates = $this->SplitTimePeriodDates($timePeriod);

            if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $startDate)) {
                $startDateFree = true;
            }

            if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $endDate)) {
                $endDateFree = true;
            }

            if ($startDateFree && $endDateFree) {
                break;
            }
        }

        if (!$startDateFree || !$endDateFree) {
            return false;
        }

        $this->temp = [];

        foreach ($freeTimePeriodes as $timePeriod => $numberOfAzubis) {

            $dates = $this->SplitTimePeriodDates($timePeriod);

            if ($numberOfAzubis < $this->abteilung->MaxAzubis) {

                // Anfang und Ende in Zeitspanne
                if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $startDate) &&
                    $this->DateInRange($dates["StartDate"], $dates["EndDate"], $endDate)) {
                    $this->temp[] = $timePeriod;
                    return true;
                }

                // Anfang in Zeitspanne
                if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $startDate)) {
                    $this->temp[] = $timePeriod;
                    continue;
                }

                // Zeitspanne zwischen Anfang und Ende
                /*if ($this->DateInRange($startDate, $endDate, $dates["StartDate"])) {
                    $this->temp[] = $timePeriod;
                    continue;
                }*/

                // Ende in Zeitspanne
                if ($this->DateInRange($dates["StartDate"], $dates["EndDate"], $endDate)) {
                    $this->temp[] = $timePeriod;
                    return true;
                }
            }

            // Beende Loop, wenn die angeforderte Zeitspanne in vorherigen Zeitspannen der Loop, aber nicht mehr in
            // der aktuellen Zeitspanne ist.
            if (!empty($this->temp)) {
                break;
            }
        }

        if (!empty($this->temp)) {
            return true;
        }

        return false;
    }

    private function GetFreeTimePeriodes() {

        $freeTimePeriodes = [];

        foreach ($this->plan as $timePeriod => $numberOfAzubis) {

            if ($numberOfAzubis < $this->abteilung->MaxAzubis) {
                $freeTimePeriodes[$timePeriod] = $numberOfAzubis;
            }
        }

        return $freeTimePeriodes;
    }

    private function SplitTimePeriodDates($timePeriod) {
        $dates = explode(" ", $timePeriod);
        return [
            "StartDate" => $dates[0],
            "EndDate"   => $dates[1]
        ];
    }


    // Ordnen
    private function DateDayAfter($date) {
        return date("Y-m-d", strtotime("$date +1 day"));
    }

    private function DateDayBefore($date) {
        return date("Y-m-d", strtotime("$date -1 day"));
    }
}
