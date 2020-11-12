<?php
/**
 * Plan.php
 *
 * Enthält die Model-Klasse Plan.
 */

namespace models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Plan {

    /**
     * @var int Die ID des Plans.
     */
    public $ID;

    /**
     * @var int Die ID des Azubi, für den dieser Plan ist.
     */
    public $ID_Azubi;

    /**
     * @var int Die ID des Ansprechpartners, der für diesen Plan verplant ist.
     */
    public $ID_Ansprechpartner;

    /**
     * @var int Die ID der Abteilung, die für diesen Plan verplant ist.
     */
    public $ID_Abteilung;

    /**
     * @var string Das Startdatum des Plans.
     */
    public $Startdatum;

    /**
     * @var string Das Enddatum des Plans.
     */
    public $Enddatum;

    /**
     * Konstruktor des Models Plan.
     *
     * @param int       $id_azubi           Die ID des Azubi, für den dieser
     *                                      Plan ist.
     * @param int       $id_ansprechpartner Die ID des Ansprechpartners, der für
     *                                      diesen Plan verplant ist.
     * @param int       $id_abteilung       Die ID der Abteilung, die für diesen
     *                                      Plan verplant ist.
     * @param string    $startdatum         Das Startdatum des Plans.
     * @param string    $enddatum           Das Enddatum des Plans.
     * @param int       $id                 Die ID des Plans.
     */
    public function __construct($id_azubi, $id_ansprechpartner, $id_abteilung, $startdatum, $enddatum, $id = 0) {
        $this->ID = intval($id);
        $this->ID_Azubi = intval($id_azubi);
        $this->ID_Ansprechpartner = (empty($id_ansprechpartner)) ? NULL : intval($id_ansprechpartner);
        $this->ID_Abteilung = intval($id_abteilung);
        $this->Startdatum = $startdatum;
        $this->Enddatum = $enddatum;
    }
}
