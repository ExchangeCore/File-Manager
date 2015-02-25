jQuery(function($) {

    var $installerFrame = $("#installer-frame");
    var ajaxRunning = 0;

    var pages = {
        databaseConfig: function() {
            $installerFrame.load("/index.php/install/db-config")
        }
    };

    $(document)
        .ajaxComplete(function(e, jqXHR) {
            var load = jqXHR.getResponseHeader('X-Install-Load');
            if (load !== null) {
                $installerFrame.load(load);
            }

            ajaxRunning--;
            if (ajaxRunning === 0) {
                NProgress.done();
            } else {
                NProgress.inc();
            }
        })
        .ajaxStart(function() {
            ajaxRunning++;
            NProgress.start();
        });

    $installerFrame.on("submit", "form", function() {
        $.post($(this).attr("action"), $(this).serialize(), function(response) {
            $installerFrame.html(response);
        });
        return false;
    });

    window.beforeunload = function() {
        return "Are you sure you want to leave this page?" + (ajaxRunning > 0 ? " The installer is currently running in the background. If you leave now you will break your installation." : "");
    };

    pages.databaseConfig();
});