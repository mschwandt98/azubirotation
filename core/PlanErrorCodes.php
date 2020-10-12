<?php
/**
 * PlanErrorCodes.php
 *
 * Enthält die abstrakte Klasse PlanErrorCodes.
 */

namespace Core;

/**
 * Dient als Enum für die Fehler-Codes der Planung.
 */
abstract class PlanErrorCodes {

    const Ausbildungszeitraum = 0;
    const AbteilungenMaxAzubis = 1;
    const PraeferierteAbteilungen = 2;
    const WochenInAbteilungen = 3;
}
