jQuery(function($) {
    $(document).ready(function() {

        $(".data-item").on("click", ".minimize-data-item", function() {
            var container = $(this).closest(".data-item");
            container.find("form").hide();
            container.find("> div").last().hide();
        });
    });
});
