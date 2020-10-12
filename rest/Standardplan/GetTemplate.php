<?php
/**
 * GetTemplate.php
 *
 * Der API-Endpunkt zum Holen des Template-Formlars zu einem Standardplan.
 */

use core\Helper\DataHelper;

if (array_key_exists("id_ausbildungsberuf", $_GET) && !empty($_GET["id_ausbildungsberuf"])) {

    include_once(dirname(dirname(__DIR__)) . "/config.php");
    include_once(HELPER . "/DataHelper.php");

    $helper = new DataHelper();
    $standardplan = array_values($helper->GetStandardPlaene(
        sanitize_string($_GET["id_ausbildungsberuf"])
    ))[0];

    $statement = $pdo->prepare("SELECT * FROM " . T_ABTEILUNGEN . " ORDER BY Bezeichnung ASC;");
    $statement->execute();
    $abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);

    $viewBag = [
        "Standardplan"  => $standardplan,
        "Abteilungen"   => $abteilungen
    ];

    ob_start("minifier");
    include_once(BASE . "/templates/Standardplan.php");
    ob_end_flush();
    exit;
}

http_response_code(400);
