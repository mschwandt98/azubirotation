<?php
/**
 * DataHelper.php
 *
 * Enthält die Klasse DataHelper.php, über welche Daten aus der Datenbank
 * einheitlich abgerufen werden können.
 *
 * TODO: Umbauen zur Database-Klasse oder Database-Klasse implementieren, über
 * welche die Database-Zugriffe ausschließlich laufen.
 */

namespace core\helper;

use PDO;

use models\Abteilung;
use models\Ansprechpartner;
use models\Ausbildungsberuf;
use models\Auszubildender;
use models\Einstellung;
use models\Phase;
use models\Plan;
use models\Standardplan;
use models\Termin;

if (!defined('BASE')) {
    include_once(BASE . '/config.php');
} else {
    include_once(dirname(dirname(__DIR__)) . '/config.php');
}

/**
 * Helper-Klasse für einen einheitlichen Abruf von Daten aus der Datenbank.
 */
class DataHelper {

    /**
     * @var PDO Das PDO Objekt zur hinterlegten Datenbank.
     */
    private $db;

    /**
     * Setzen des Datenbank-Objekts.
     */
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Holt alle Abteilungen aus der Datenbank und erstellt aus jedem Datensatz
     * ein "Abteilung"-Objekt.
     *
     * @param int $id Die ID der Abteilung, die geholt werden soll. Wenn nicht
     *                gesetzt, werden alle Abteilungen geholt.
     *
     * @return Abteilung|Abteilung[] Die Abteilung bzw die Abteilungen.
     */
    public function GetAbteilungen($id = null) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_ABTEILUNGEN .
            $this->CreateWhereId($id) .
            'ORDER BY Bezeichnung ASC;'
        );
        $statement->execute();
        $abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($abteilungen as $key => $abteilung) {
            $abteilungen[$key] = new Abteilung(
                $abteilung['Bezeichnung'],
                $abteilung['MaxAzubis'],
                $abteilung['Farbe'],
                $abteilung['ID']
            );
        }

        if (empty($abteilungen)) return [];
        return (empty($id)) ? $abteilungen : $abteilungen[0];
    }

    /**
     * Holt alle Ansprechpartner aus der Datenbank und erstellt aus jedem
     * Datensatz ein "Ansprechpartner"-Objekt.
     *
     * @param int $id Die ID des Ansprechpartners, der geholt werden soll.
     *                Wenn nicht gesetzt, werden alle Ansprechpartner geholt.
     *
     * @return Ansprechpartner|Ansprechpartner[] Der bzw die Ansprechpartner.
     */
    public function GetAnsprechpartner($id = null) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_ANSPRECHPARTNER .
            $this->CreateWhereId($id) .
            'ORDER BY Name ASC;'
        );
        $statement->execute();
        $ansprechpartner = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ansprechpartner as $key => $person) {
            $ansprechpartner[$key] = new Ansprechpartner(
                $person['Name'],
                $person['Email'],
                $person['ID_Abteilung'],
                $person['ID']
            );
        }

        if (empty($ansprechpartner)) return [];
        return (empty($id)) ? $ansprechpartner : $ansprechpartner[0];
    }

    /**
     * Holt alle Ausbildungsberufe aus der Datenbank und erstellt aus jedem
     * Datensatz ein "Ausbildungsberuf"-Objekt.
     *
     * @param int $id Die ID des Ansprechpartners, der geholt werden soll.
     *                Wenn nicht gesetzt, werden alle Ansprechpartner geholt.
     *
     * @return Ausbildungsberuf|Ausbildungsberuf[] Der Ausbildungsberuf bzw die
     *                                             Ausbildungsberufe.
     */
    public function GetAusbildungsberufe($id = null) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_AUSBILDUNGSBERUFE .
            $this->CreateWhereId($id) .
            'ORDER BY Bezeichnung ASC;'
        );
        $statement->execute();
        $ausbildungsberufe = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ausbildungsberufe as $key => $beruf) {
            $ausbildungsberufe[$key] = new Ausbildungsberuf(
                $beruf['Bezeichnung'],
                $beruf['ID']
            );
        }

        if (empty($ausbildungsberufe)) return [];
        return (empty($id)) ? $ausbildungsberufe : $ausbildungsberufe[0];
    }

    /**
     * Holt alle Azubis aus der Datenbank und erstellt aus jedem
     * Datensatz ein "Auszubildender"-Objekt.
     *
     * @param int $id Die ID des Azubis, der geholt werden soll. Wenn nicht
     *                gesetzt, werden alle Azubis geholt.
     *
     * @return Auszubildender|Auszubildender[] Der Azubi bzw die Azubis.
     */
    public function GetAzubis($id = null) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_AUSZUBILDENDE .
            $this->CreateWhereId($id) .
            'ORDER BY Nachname ASC;'
        );
        $statement->execute();
        $azubis = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($azubis as $key => $azubi) {
            $azubis[$key] = new Auszubildender(
                $azubi['Vorname'],
                $azubi['Nachname'],
                $azubi['Email'],
                $azubi['ID_Ausbildungsberuf'],
                $azubi['Ausbildungsstart'],
                $azubi['Ausbildungsende'],
                $azubi['ID']
            );
        }

        if (empty($azubis)) return [];
        return (empty($id)) ? $azubis : $azubis[0];
    }

    /**
     * Holt alle Standardpläne aus der Datenbank und erstellt aus jedem
     * Datensatz ein "Standardplan"-Objekt.
     *
     * @param int $id Die ID des Ausbildungsberufes, zu dem der Standardplan
     *                geholt werden soll. Wenn nicht gesetzt, werden alle
     *                Standardpläne geholt.
     *
     * @return Standardplan[] Die Standardpläne. Jeder Standardplan ist im Array
     *                        unter dem Key mit der Bezeichnung des zugehörigen
     *                        Ausbildungsberufes gelistet.
     */
    public function GetStandardPlaene($id_ausbildungsberuf = null) {

        $sql_where = '';

        if ($id_ausbildungsberuf !== null) {
            $id_ausbildungsberuf = intval($id_ausbildungsberuf);
            $sql_where = ' WHERE ab.ID = ' . $id_ausbildungsberuf;
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
                $phase['ID_Abteilung'],
                $phase['Abteilung'],
                $phase['AnzahlWochen'],
                $phase['Praeferieren'],
                $phase['Optional']
            );

            if (array_key_exists($phase['Ausbildungsberuf'], $standardplaene)) {
                $standardplaene[$phase['Ausbildungsberuf']]->Phasen[] = $needed_phase;
            } else {
                $standardplaene[$phase['Ausbildungsberuf']] = new Standardplan(
                    $phase['ID_Ausbildungsberuf'],
                    $phase['Ausbildungsberuf'],
                    [ $needed_phase ]
                );
            }
        }

        // TODO: if (!empty($id_ausbildungsberuf)) return array_shift($standardplaene);
        // + Stellen anpassen, an denen GetStandardplaene aufgerufen wird
        return $standardplaene;
    }

    /**
     * Holt alle Pläne aus der Datenbank und erstellt zu jedem Datensatz ein
     * "Plan"-Objekt.
     *
     * @return Plan[] Die Pläne sortiert nach der ID der Azubis, zweitrangig
     *                nach dem Startdatum der einzelnen Pläne.
     */
    public function GetPlaene() {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_PLAENE .
            ' ORDER BY ID_Auszubildender ASC, Startdatum ASC;'
        );
        $statement->execute();
        $plaene = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($plaene as $key => $plan) {
            $plaene[$key] = new Plan(
                $plan['ID_Auszubildender'],
                $plan['ID_Ansprechpartner'],
                $plan['ID_Abteilung'],
                $plan['Startdatum'],
                $plan['Enddatum'],
                $plan['ID']
            );
        }

        return $plaene;
    }

    /**
     * Holt eine Einstellung aus der Datenbank.
     *
     * @param string $name Der Name der Einstellung.
     *
     * @return Einstellung Die angeforderte Einstellung.
     */
    public function GetSetting($name) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_SETTINGS .
            ' WHERE name = :name;'
        );
        $statement->execute([ ':name' => $name ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return new Einstellung($result['name'], $result['value']);
    }

    function GetTermin($id_plan) {

        $statement = $this->db->prepare(
            'SELECT * FROM ' . T_TERMINE . '
            WHERE ID_Plan = :id_plan'
        );
        $statement->execute([ ':id_plan' => intval($id_plan) ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) return false;

        return new Termin(
            $result['Bezeichnung'],
            $result['Separat'],
            $result['ID_Plan'],
            $result['ID']
        );
    }

    /**
     * Aktualisiert den Wert einer Einstellung.
     *
     * @param string $name Der Name der zu ändernden Einstellung.
     * @param mixed $value Der Wert, der gesetzt werden soll.
     *
     * @return bool Der Status, ob die Ausführung erfolgreich war.
     */
    public function UpdateSetting($name, $value) {

        return ($this->db->prepare(
            'UPDATE ' . T_SETTINGS .
            ' SET value = :value' .
            ' WHERE name = :name;'
        ))->execute([ ':value' => $value, ':name' => $name ]);
    }

    /**
     * Erstellt einen SQL-WHERE Substring.
     *
     * @param int $id Die ID, nach der gesucht werden soll.
     *
     * @return string Wenn $id null ist, wird ein Leerzeichen zurückgegeben.
     *                Ansonsten wird " WHERE ID = [$id] " zurückgegeben.
     */
    private function CreateWhereId($id) {
        return (empty($id)) ? ' ' : ' WHERE ID = ' . intval($id) . ' ';
    }
}
