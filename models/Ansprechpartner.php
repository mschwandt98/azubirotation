<?php
namespace Models;

class Ansprechpartner {

    public $Name;
    public $Email;
    public $ID_Abteilung;
    public $ID;

    function __construct($name, $email, $id_abteilung, $id = 0) {
        $this->Name = $name;
        $this->Email = $email;
        $this->ID_Abteilung = intval($id_abteilung);
        $this->ID = intval($id);
    }
}
