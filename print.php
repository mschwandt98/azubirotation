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

<link rel="stylesheet" type="text/css" href="assets/css/print.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div id="BlackScreen"></div>
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
    table.find("tr.space-row").remove();

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
    var pageWidth = 1090;
    var pageCount = Math.ceil(table.outerWidth() / (pageWidth - 100));
    var printWrap = $("#Plan");
    var printPage;

    var kuerzelEntrys = table.clone().find("tr").children('.kuerzel, .ausbildungsberuf').remove();
    var kuerzelTable = $("<table></table>");
    kuerzelEntrys.each(function (index) {
        var entry = $(kuerzelEntrys[index]);
        kuerzelTable.append(
            $("<tr></tr>").append(entry)
        );
    });

    kuerzelTable.css({
        marginTop: table.find(".top-left-sticky").first().outerHeight() - 1 + "px",
        width: "100px"
    });

    kuerzelTableWrapper = $("<div></div>").css({
        backgroundColor: "white",
        zIndex: "999"
    }).append(kuerzelTable);

    var lastLeftValue = 0;
    for (var i = 0; i < pageCount; i++) {
        let wrapperDiv = $("<div></div>").css({
            "display": "flex",
            "overflow": "hidden",
            "width": pageWidth,
            "page-break-before": i === 0 ? "auto" : "always"
        });

        printPage = wrapperDiv.appendTo(printWrap);

        let clonedTable = table.clone().removeAttr("id").css({
            "position": "relative",
            "left": -lastLeftValue + "px"
        });

        lastLeftValue += i === 0 ? pageWidth + 100 : pageWidth - 100;

        if (i !== 0) {
            printPage.append(kuerzelTableWrapper.clone());
            clonedTable.css({
                marginLeft: "100px"
            });
        }

        printPage.append(clonedTable);
    }
    table.hide();

    var tableParts = $("#Plan > div:not(:first)");
    tableParts.css({ marginTop: $("#Header").outerHeight() });

    window.print();
    window.addEventListener("afterprint", (event) => {
        window.close();
    });
</script>

<?php ob_end_flush(); ?>
