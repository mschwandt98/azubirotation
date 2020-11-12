<?php
/**
 * Termin.php
 *
 * Enthält die Model-Klasse Termin.
 */

namespace models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Termin {

    /**
     * @var int Die ID des Termins.
     */
    public $ID;

    /**
     * @var string Die Bezeichnung des Termins.
     */
    public $Bezeichnung;

    /**
     * @var bool Der Status, ob es ein einzelner Termin ist oder nicht.
     */
    public $Separat;

    /**
     * @var int Die ID des Plans, zu dem der Termin gehört.
     */
    public $ID_Plan;

    /**
     * Konstruktor des Models Termins.
     *
     * @param string    $bezeichnung    Die Bezeichnung des Termins.
     * @param bool      $separat        Der Status, ob es ein einzelner Termin
     *                                  ist oder nicht.
     * @param int       $id_plan        Die ID des Plans, zu dem der Termin
     *                                  gehört.
     * @param int       $id             Die ID des Termins. (Optional)
     */
    function __construct($bezeichnung, $separat, $id_plan, $id = 0) {
        $this->Bezeichnung = strval($bezeichnung);
        $this->Separat = filter_var($separat, FILTER_VALIDATE_BOOLEAN);
        $this->ID_Plan = intval($id_plan);
        $this->ID = intval($id);
    }
}
