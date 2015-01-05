jQuery(function($) {

    var $installerFrame = $("#installer-frame");

    var pages = {
        databaseConfig: function() {
            $installerFrame.load("/index.php?r=install%2Fdb-config")
        }
    };

    $installerFrame.on("submit", "form", function() {
        $.post($(this).attr("action"), $(this).serialize(), function(response) {
            $installerFrame.html(response);
        });
        return false;
    });

    pages.databaseConfig();
});