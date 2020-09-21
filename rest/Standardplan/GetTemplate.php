<?php
use Models\Standardplan;
use Models\Phase;

if (array_key_exists("id_ausbildungsberuf", $_GET) && !empty($_GET["id_ausbildungsberuf"])) {

    include_once(dirname(dirname(__DIR__)) . "/config.php");

    global $pdo;

    $sql_where = " WHERE ab.ID = " . intval($_GET["id_ausbildungsberuf"]);

    $statement = $pdo->prepare(
        "SELECT sp.*, ab.Bezeichnung AS Ausbildungsberuf, a.Bezeichnung AS Abteilung
        FROM standardpläne sp
        JOIN ausbildungsberufe ab ON sp.ID_Ausbildungsberuf = ab.ID
        JOIN abteilungen a ON sp.ID_Abteilung = a.ID
        $sql_where
        ORDER BY ab.Bezeichnung ASC;"
    );

    $statement->execute();
    $standardplan_phasen = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($standardplan_phasen as $phase) {

        $phase = (object) $phase;

        $needed_phase = new Phase(
            $phase->ID_Abteilung,
            $phase->Abteilung,
            $phase->AnzahlWochen
        );

        if (!empty($standardplan)) {
            $standardplan->Phasen[] = $needed_phase;
        } else {
            $standardplan = new Standardplan(
                $phase->ID_Ausbildungsberuf,
                $phase->Ausbildungsberuf,
                [ $needed_phase ]
            );
        }
    }

    $statement = $pdo->prepare("SELECT * FROM " . T_ABTEILUNGEN . " ORDER BY Bezeichnung ASC;");
    $statement->execute();
    $abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);

    $viewBag = [
        "Standardplan"  => $standardplan,
        "Abteilungen"   => $abteilungen
    ];

    ob_start();
    include_once(BASE . "/templates/Standardplan.php");
    exit(ob_get_clean());
}

http_response_code(400);
