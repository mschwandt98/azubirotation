<?php
namespace Models;

class Standardplan {

    public $ID_Ausbildungsberuf;
    public $Ausbildungsberuf;
    public $Phasen;

    function __construct($id_ausbildungsberuf, $ausbildungsberuf, $phasen) {
        $this->ID_Ausbildungsberuf = intval($id_ausbildungsberuf);
        $this->Ausbildungsberuf = $ausbildungsberuf;
        $this->Phasen = $phasen;
    }
}
