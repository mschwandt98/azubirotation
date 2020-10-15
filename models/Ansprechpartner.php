<?php
/**
 * Ansprechpartner.php
 *
 * Enthält die Model-Klasse Ansprechpartner.
 */

namespace models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Ansprechpartner {

    /**
     * @var string $Name Der Name des Ansprechpartners.
     */
    public $Name;

    /**
     * @var string $Email Die Email des Ansprechpartners.
     */
    public $Email;

    /**
     * @var int $ID_Abteilung Die ID der Abteilung, für die der Ansprechpartner
     *                        verplant werden darf.
     */
    public $ID_Abteilung;

    /**
     * @var int $ID Die ID des Ansprechpartners.
     */
    public $ID;

    /**
     * Konstruktor des Models Ansprechpartner.
     *
     * @param string    $name           Der Name des Ansprechpartners.
     * @param string    $email          Die Email des Ansprechpartners.
     * @param int       $id_abteilung   Die ID der Abteilung, für die der
     *                                  Ansprechpartner verplant werden darf.
     * @param int       $id             Die ID des Ansprechpartners. Optional,
     *                                  falls der Ansprechpartner noch nicht
     *                                  gespeichert ist.
     */
    function __construct($name, $email, $id_abteilung, $id = 0) {
        $this->Name = $name;
        $this->Email = $email;
        $this->ID_Abteilung = intval($id_abteilung);
        $this->ID = intval($id);
    }
}
