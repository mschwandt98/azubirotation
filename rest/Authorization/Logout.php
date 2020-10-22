<?php
/**
 * Logout.php
 *
 * Der API-Endpunkt zum Abmelden.
 */

session_start();
session_unset();
session_destroy();
