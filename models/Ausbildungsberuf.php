<?php
/**
 * Ausbildungsberuf.php
 *
 * Enthält die Model-Klasse Ausbildungsberuf.
 */

namespace models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Ausbildungsberuf {

    /**
     * @var string $Bezeichnung Die Bezeichnung des Ausbildungsberufs.
     */
    public $Bezeichnung;

    /**
     * @var int Die ID des Ausbildungsberufs.
     */
    public $ID;

    /**
     * Konstruktor des Models Ausbildungsberuf.
     *
     * @param string    $bezeichnung    Die Bezeichnung des Ausbildungsberufs.
     * @param int       $id             Die ID des Ausbildungsberufs.
     */
    function __construct($bezeichnung, $id = 0) {
        $this->Bezeichnung = $bezeichnung;
        $this->ID = intval($id);
    }
}
