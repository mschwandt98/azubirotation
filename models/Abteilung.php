<?php
namespace Models;

class Abteilung {

    public $Bezeichnung;
    public $MaxAzubis;
    public $Farbe;
    public $ID;

    function __construct($bezeichnung, $maxAzubis, $farbe, $id = 0) {
        $this->Bezeichnung = $bezeichnung;
        $this->MaxAzubis = intval($maxAzubis);
        $this->Farbe = $farbe;
        $this->ID = intval($id);
    }
}
