(function($) {
    if ($('#dm_admin_content').length >0) {
        $(".fancybox").fancybox({
            margin: [50,50,50, 50]
        });
    };

    $('div.dm.dm_media_file_dialog').live('dmAjaxResponse', function() {
        $(this).find(".fancybox").fancybox({
            margin: [50,50,50, 50]
        });       
    });
})(jQuery);