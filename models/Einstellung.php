<?php
/**
 * Einstellung.php
 *
 * Enthält die Model-Klasse Einstellung.
 */

namespace Models;

/**
 * Model-Klasse für die Vereinheitlichung der Daten.
 */
class Einstellung {

    /**
     * @var string Der Name der Einstellung.
     */
    public $Name;

    /**
     * @var string Der Wert der Einstellung.
     */
    public $Value;

    /**
     * Der Konstruktor des Models Einstellung.
     *
     * @param string $name  Der Name der Einstellung.
     * @param string $value Der Wert der Einstellung.
     */
    function __construct($name, $value) {
        $this->Name = strval($name);
        $this->Value = $value;
    }
}
