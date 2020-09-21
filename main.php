<div class="grid">
    <div><?php include_once("templates/Abteilungen.php"); ?></div>
    <div><?php include_once("templates/Ausbildungsberufe.php"); ?></div>
    <div><?php include_once("templates/Ansprechpartner.php"); ?></div>
    <div><?php include_once("templates/Azubis.php"); ?></div>
    <div><?php include_once("templates/Standardplaene.php"); ?></div>
</div>




<div style="margin-top: 100px; text-align: center;">
    <button onclick="StartPlanning();">Start planning</button>
</div>
<div id="Plan"></div>

<script>
    function StartPlanning() {

        jQuery.get("core/CreatePlan.php", function(response){
            $("#Plan").empty();
            $("#Plan").append(response);
        });
    }
</script>
