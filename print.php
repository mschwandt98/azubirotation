<?php
/**
 * print.php
 *
 * Erstellt eine druckbare Versions des Plans.
 */

session_start();
include(__DIR__ . '/config.php');

ob_start();
include_once(__DIR__ . '/core/Plan.php');
$plan = ob_get_clean();

ob_start();
include_once(__DIR__ . '/footer.php');
$legende = ob_get_clean();

ob_start("minifier");
?>

<link rel="stylesheet" href="assets/css/print.css">

<h1>Ausbildungsplaner</h1>
<div id="Legende">
    <?= $legende; ?>
</div>
<div id="Plan">
    <?= $plan; ?>
</div>

<script>
    window.print();

    window.addEventListener('afterprint', (event) => {
        window.close();
    });
</script>

<?php ob_end_flush(); ?>
