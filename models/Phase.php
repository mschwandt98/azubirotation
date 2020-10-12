<?php
/**
 * Phase.php
 *
 * Enthält die Model-Klasse Phase.
 */

namespace Models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Phase {

    /**
     * @var int Die ID der Abteilung dieser Phase ist.
     */
    public $ID_Abteilung;

    /**
     * @var string Die Bezeichnung der Abteilung dieser Phase ist.
     * TODO: Kann entfernt werden. Jedoch Anpassungen müssen gemacht werden!
     */
    public $Abteilung;

    /**
     * @var int Die Anzahl an Wochen, die diese Phase dauert.
     */
    public $Wochen;

    /**
     * @var bool Der Status, ob die Abteilung präferiert werden soll. Präferiert
     *           heißt in diesem Zusammenhang, dass diese Abteilung für den
     *           Anfang der Ausbildung verplant werden soll.
     */
    public $Praeferieren;

    /**
     * @var bool Der Status, ob die Abteilung optional ist. Optional heißt in
     *           diesem Zusammenhang, dass diese Abteilung nicht unbedingt im
     *           Plan des Azubis auftauchen muss. Sie kann als Lückenfüller
     *           angesehen werden.
     */
    public $Optional;

    /**
     * Konstruktor des Models Phase.
     *
     * @param int       $id_abteilung   Die ID der Abteilung dieser Phase ist.
     * @param string    $abteilung      Die Bezeichnung der Abteilung dieser
     *                                  Phase ist.
     * @param int       $wochen         Die Anzahl an Wochen, die diese Phase
     *                                  dauert.
     * @param bool      $praeferieren   Der Status, ob die Abteilung präferiert
     *                                  werden soll.
     * @param bool      $optional       Der Status, ob die Abteilung optional
     *                                  ist.
     */
    function __construct($id_abteilung, $abteilung, $wochen, $praeferieren, $optional) {
        $this->ID_Abteilung = intval($id_abteilung);
        $this->Abteilung = $abteilung;
        $this->Wochen = intval($wochen);
        $this->Praeferieren = filter_var($praeferieren, FILTER_VALIDATE_BOOLEAN);
        $this->Optional = filter_var($optional, FILTER_VALIDATE_BOOLEAN);
    }
}
