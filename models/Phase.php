<?php
namespace Models;

class Phase {

    public $ID_Abteilung;
    public $Abteilung;
    public $Wochen;
    public $Praeferieren;
    public $Optional;

    function __construct($id_abteilung, $abteilung, $wochen, $praeferieren, $optional) {
        $this->ID_Abteilung = intval($id_abteilung);
        $this->Abteilung = $abteilung;
        $this->Wochen = intval($wochen);
        $this->Praeferieren = filter_var($praeferieren, FILTER_VALIDATE_BOOLEAN);
        $this->Optional = filter_var($optional, FILTER_VALIDATE_BOOLEAN);
    }
}
