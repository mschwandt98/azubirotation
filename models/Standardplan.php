<?php
/**
 * Standardplan.php
 *
 * Enthält die Model-Klasse Standardplan.
 */

namespace Models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Standardplan {

    /**
     * @var int Die ID des Ausbildungsberufes, für den dieser Standardplan ist.
     */
    public $ID_Ausbildungsberuf;

    /**
     * @var string Die Bezeichnung des Ausbildungsberufes, für den dieser
     *             Standardplan ist.
     */
    public $Ausbildungsberuf;

    /**
     * @var Phase[] Die einzelnen Phasen im Standardplan.
     */
    public $Phasen;

    /**
     * Konstruktor des Models Standardplan.
     *
     * @param int       $id_ausbildungsberuf    Die ID des Ausbildungsberufes,
     *                                          für den dieser Standardplan ist.
     * @param string    $ausbildungsberuf       Die Bezeichnung des
     *                                          Ausbildungsberufes, für den
     *                                          dieser Standardplan ist.
     * @param Phase[]   $phasen                 Die einzelnen Phasen im
     *                                          Standardplan.
     */
    function __construct($id_ausbildungsberuf, $ausbildungsberuf, $phasen) {
        $this->ID_Ausbildungsberuf = intval($id_ausbildungsberuf);
        $this->Ausbildungsberuf = $ausbildungsberuf;
        $this->Phasen = $phasen;
    }
}
