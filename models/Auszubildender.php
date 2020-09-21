<?php
namespace Models;

class Auszubildender {

    public $Vorname;
    public $Nachname;
    public $Email;
    public $ID_Ausbildungsberuf;
    public $Ausbildungsstart;
    public $Ausbildungsende;
    public $ID;

    function __construct($vorname, $nachname, $email, $id_ausbildungsberuf, $ausbildungsstart, $ausbildungsende, $id = 0) {
        $this->Vorname = $vorname;
        $this->Nachname = $nachname;
        $this->Email = $email;
        $this->ID_Ausbildungsberuf = intval($id_ausbildungsberuf);
        $this->Ausbildungsstart = $ausbildungsstart;
        $this->Ausbildungsende = $ausbildungsende;
        $this->ID = intval($id);
    }
}
