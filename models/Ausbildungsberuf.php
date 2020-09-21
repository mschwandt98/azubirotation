<?php
namespace Models;

class Ausbildungsberuf {

    public $Bezeichnung;
    public $ID;

    function __construct($bezeichnung, $id = 0) {
        $this->Bezeichnung = $bezeichnung;
        $this->ID = intval($id);
    }
}
