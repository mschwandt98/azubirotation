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
include_once(__DIR__ . '/legende.php');
$legende = ob_get_clean();

ob_start("minifier");
?>

<style>
    <?php include_once(BASE . 'assets/css/print.css'); ?>
</style>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- <div id="BlackScreen"></div> -->
<div id="Header">
    <h1>Ausbildungsplan</h1>
    <div id="Legende">
        <?= $legende; ?>
    </div>
    <br>
</div>
<div id="Plan">
    <?= $plan; ?>
</div>

<script>
    var table = $("#Plan table");

    /* Kalenderwochen verstecken */
    var pastWeek = table.find(".current-week").prev();
    var weeksToHide = 0;
    while (pastWeek.hasClass("month")) {
        pastWeek.hide();
        pastWeek = pastWeek.prev();
        weeksToHide++;
    }

    /* Wochen in Azubireihen verstecken */
    var rows = table.find(".azubi");
    rows.each(index => {
        let phases = $(rows[index]).find(".plan-phase");
        phases.each(i => {
            if (i >= weeksToHide) return false;
            $(phases[i]).remove();
        });
    });

    /* Header der Tabelle verstecken (Monat Jahr) */
    var headerColumns = table.find("thead").find("tr").eq(0).find("th:not(.top-left-sticky)");
    var counter = weeksToHide;
    headerColumns.each(index => {

        column = $(headerColumns[index]);
        counter = counter - column.prop("colSpan");

        if (counter <= 0) {
            column.attr("colSpan", Math.abs(counter));
            return false;
        } else {
            column.remove();
        }
    });

    /* Tabelle brechen für Druck */
    var tableWidth = table.outerWidth(),
        pageWidth = 1090,
        pageCount = Math.ceil(tableWidth / pageWidth),
        printWrap = $("#Plan"),
        i,
        printPage;
    for (i = 0; i < pageCount; i++) {
        printPage = $("<div></div>").css({
            "overflow": "hidden",
            "width": pageWidth,
            "page-break-before": i === 0 ? "auto" : "always"
        }).appendTo(printWrap);
        table.clone().removeAttr("id").appendTo(printPage).css({
            "position": "relative",
            "left": -i * pageWidth
        });
    }
    table.hide();

    var tableParts = $("#Plan > div:not(:first)");
    tableParts.css({ marginTop: $("#Header").outerHeight() });

    // window.print();
    window.addEventListener("afterprint", (event) => {
        window.close();
    });
</script>

<?php ob_end_flush(); ?>
