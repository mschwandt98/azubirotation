<?php
namespace Core;

use PDO;

use Models\Abteilung;
use Models\Ansprechpartner;
use Models\Ausbildungsberuf;
use Models\Auszubildender;
use Models\Phase;
use Models\Standardplan;
use Models\Plan;

if (!defined("BASE")) {
    include_once(BASE . "/config.php");
}
else {
    include_once(dirname(__DIR__) . "/config.php");
}

include_models();

class Helper {

    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function GetAbteilungen($id = null) {

        $statement = $this->db->prepare(
            "SELECT * FROM " . T_ABTEILUNGEN .
            $this->CreateWhereId($id) .
            "ORDER BY Bezeichnung ASC;"
        );
        $statement->execute();
        $abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($abteilungen as $key => $abteilung) {
            $abteilungen[$key] = new Abteilung(
                $abteilung["Bezeichnung"],
                $abteilung["MaxAzubis"],
                $abteilung["Farbe"],
                $abteilung["ID"]
            );
        }

        return $abteilungen;
    }

    public function GetAnsprechpartner($id = null) {

        $statement = $this->db->prepare(
            "SELECT * FROM " . T_ANSPRECHPARTNER .
            $this->CreateWhereId($id) .
            "ORDER BY Name ASC;"
        );
        $statement->execute();
        $ansprechpartner = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ansprechpartner as $key => $person) {
            $ansprechpartner[$key] = new Ansprechpartner(
                $person["Name"],
                $person["Email"],
                $person["ID_Abteilung"],
                $person["ID"]
            );
        }

        return $ansprechpartner;
    }

    public function GetAusbildungsberufe($id = null) {

        $statement = $this->db->prepare(
            "SELECT * FROM " . T_AUSBILDUNGSBERUFE .
            $this->CreateWhereId($id) .
            "ORDER BY Bezeichnung ASC;"
        );
        $statement->execute();
        $ausbildungsberufe = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ausbildungsberufe as $key => $beruf) {
            $ausbildungsberufe[$key] = new Ausbildungsberuf(
                $beruf["Bezeichnung"],
                $beruf["ID"]
            );
        }

        return $ausbildungsberufe;
    }

    public function GetAzubis($id = null) {

        $statement = $this->db->prepare(
            "SELECT * FROM " . T_AUSZUBILDENDE .
            $this->CreateWhereId($id) .
            "ORDER BY Nachname ASC;"
        );
        $statement->execute();
        $azubis = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($azubis as $key => $azubi) {
            $azubis[$key] = new Auszubildender(
                $azubi["Vorname"],
                $azubi["Nachname"],
                $azubi["Email"],
                $azubi["ID_Ausbildungsberuf"],
                $azubi["Ausbildungsstart"],
                $azubi["Ausbildungsende"],
                $azubi["ID"]
            );
        }

        return $azubis;
    }

    public function GetStandardPlaene($id = null) {

        $sql_where = "";

        if ($id !== null) {
            $id = intval($id);
            $sql_where = " WHERE ab.ID = $id";
        }

        $statement = $this->db->prepare(
            "SELECT sp.*, ab.Bezeichnung AS Ausbildungsberuf, a.Bezeichnung AS Abteilung
            FROM standardpläne sp
            JOIN ausbildungsberufe ab ON sp.ID_Ausbildungsberuf = ab.ID
            JOIN abteilungen a ON sp.ID_Abteilung = a.ID
            $sql_where
            ORDER BY ab.Bezeichnung ASC;"
        );
        $statement->execute();
        $standardplan_phasen = $statement->fetchAll(PDO::FETCH_ASSOC);
        $standardplaene = [];

        foreach ($standardplan_phasen as $phase) {

            $needed_phase = new Phase(
                $phase["ID_Abteilung"],
                $phase["Abteilung"],
                $phase["AnzahlWochen"],
                $phase["Praeferieren"],
                $phase["Optional"]
            );

            if (array_key_exists($phase["Ausbildungsberuf"], $standardplaene)) {
                $standardplaene[$phase["Ausbildungsberuf"]]->Phasen[] = $needed_phase;
            } else {
                $standardplaene[$phase["Ausbildungsberuf"]] = new Standardplan(
                    $phase["ID_Ausbildungsberuf"],
                    $phase["Ausbildungsberuf"],
                    [ $needed_phase ]
                );
            }
        }

        return $standardplaene;
    }

    public function GetPlaene() {

        $statement = $this->db->prepare(
            "SELECT * FROM " . T_PLAENE .
            " ORDER BY ID_Auszubildender ASC, Startdatum ASC;"
        );
        $statement->execute();
        $plaene = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($plaene as $key => $plan) {
            $plaene[$key] = new Plan(
                $plan["ID"],
                $plan["ID_Auszubildender"],
                $plan["ID_Ansprechpartner"],
                $plan["ID_Abteilung"],
                $plan["Startdatum"],
                $plan["Enddatum"]
            );
        }

        return $plaene;
    }

    private function CreateWhereId($id) {
        if ($id !== null) {
            return " WHERE ID = $id ";
        }

        return " ";
    }
}
