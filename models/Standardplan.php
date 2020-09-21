<?php
namespace Models;

class Standardplan {

    public $ID_Ausbildungsberuf;
    public $Ausbildungsberuf;
    public $Phasen;

    function __construct($ID_Ausbildungsberuf, $Ausbildungsberuf, $Phasen) {
        $this->ID_Ausbildungsberuf = intval($ID_Ausbildungsberuf);
        $this->Ausbildungsberuf = $Ausbildungsberuf;
        $this->Phasen = $Phasen;
    }
}
