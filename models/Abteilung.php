<?php
/**
 * Abteilung.php
 *
 * Enthält die Model-Klasse Abteilung.
 */

namespace Models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Abteilung {

    /**
     * @var string Die Bezeichnung der Abteilung.
     */
    public $Bezeichnung;

    /**
     * @var int Die maximale Anzahl an Azubis, die gleichzeitig in der Abteilung
     *          verplant sein dürfen.
     */
    public $MaxAzubis;

    /**
     * @var string Die Farbe, mit der die Abteilung in der Planung dargestellt
     *             wird.
     */
    public $Farbe;

    /**
     * @var int Die ID der Abteilung.
     */
    public $ID;

    /**
     * Konstruktor des Models Abteilung.
     *
     * @param string    $bezeichnung    Die Bezeichnung der Abteilung.
     * @param int       $maxAzubis      Die maximale Anzahl an Azubis, die
     *                                  gleichzeitig in der Abteilung verplant
     *                                  sein dürfen.
     * @param string    $farbe          Die Farbe, mit der die Abteilung in der
     *                                  Planung dargestellt wird.
     * @param int       $id             Die ID der Abteilung. Optional, falls
     *                                  die Abteilung noch nicht gespeichert
     *                                  ist.
     */
    function __construct($bezeichnung, $maxAzubis, $farbe, $id = 0) {
        $this->Bezeichnung = $bezeichnung;
        $this->MaxAzubis = intval($maxAzubis);
        $this->Farbe = $farbe;
        $this->ID = intval($id);
    }
}
