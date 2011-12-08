(function($) {
    $('div.dm.dm_widget_edit_dialog_wrap').live('dmAjaxResponse', function() {
        var $tabs = $(this).find('.two-columns-tabular');
        if ($tabs.length == 0) return;
        $tabs.dmCoreTabForm();        
    });
})(jQuery);