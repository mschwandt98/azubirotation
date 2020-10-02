<?php
namespace Models;

class Plan {

    public $ID;
    public $ID_Azubi;
    public $ID_Ansprechpartner;
    public $ID_Abteilung;
    public $Startdatum;
    public $Enddatum;

    public function __construct($id_azubi, $id_ansprechpartner, $id_abteilung, $startdatum, $enddatum, $markierung, $id = 0) {
        $this->ID = intval($id);
        $this->ID_Azubi = intval($id_azubi);
        $this->ID_Ansprechpartner = (empty($id_ansprechpartner)) ? NULL : intval($id_ansprechpartner);
        $this->ID_Abteilung = intval($id_abteilung);
        $this->Startdatum = $startdatum;
        $this->Markierung = $markierung;
        $this->Enddatum = $enddatum;
    }
}
