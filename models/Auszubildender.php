<?php
/**
 * Auszubildender.php
 *
 * Enthält die Model-Klasse Auszubildender.
 */

namespace models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Auszubildender {

    /**
     * @var string Der Vorname des Azubis.
     */
    public $Vorname;

    /**
     * @var string Der Nachname des Azubis.
     */
    public $Nachname;

    /**
     * @var string Das Kürzel des Azubis.
     */
    public $Kuerzel;

    /**
     * @var string Die Email des Azubis.
     */
    public $Email;

    /**
     * @var int Die ID des Ausbildungsberufes des Azubis.
     */
    public $ID_Ausbildungsberuf;

    /**
     * @var string Das Datum, an dem der Azubi seine Ausbildung startet.
     */
    public $Ausbildungsstart;

    /**
     * @var string Das Datum, an dem der Azubi seine Ausbildung beendet.
     */
    public $Ausbildungsende;

    /**
     * @var int Die ID des Azubis.
     */
    public $ID;

    /**
     * Der Konstruktor des Models Auszubildender.
     *
     * @param string    $vorname                Der Vornames des Azubis.
     * @param string    $nachname               Der Nachname des Azubis.
     * @param string    $kuerzel                Das Kürzel des Azubis.
     * @param string    $email                  Die Email des Azubis.
     * @param int       $id_ausbildungsberuf    Die ID des Ausbildungsberufes
     *                                          des Azubis.
     * @param string    $ausbildungsstart       Das Datum, an dem der Azubi
     *                                          seine Ausbildung startet.
     * @param string    $ausbildungsende        Das Datum, an dem der Azubi
     *                                          seine Ausbildung beendet.
     * @param int       $id                     Die ID des Azubis.
     */
    function __construct($vorname, $nachname, $kuerzel, $email, $id_ausbildungsberuf, $ausbildungsstart, $ausbildungsende, $id = 0) {
        $this->Vorname = $vorname;
        $this->Nachname = $nachname;
        $this->Kuerzel = $kuerzel;
        $this->Email = $email;
        $this->ID_Ausbildungsberuf = intval($id_ausbildungsberuf);
        $this->Ausbildungsstart = $ausbildungsstart;
        $this->Ausbildungsende = $ausbildungsende;
        $this->ID = intval($id);
    }
}
