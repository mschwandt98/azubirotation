<?php
namespace Models;

class Phase {

    public $ID_Abteilung;
    public $Abteilung;
    public $Wochen;

    function __construct($ID_Abteilung, $Abteilung, $Wochen) {
        $this->ID_Abteilung = intval($ID_Abteilung);
        $this->Abteilung = $Abteilung;
        $this->Wochen = intval($Wochen);
    }
}
