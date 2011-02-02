(function($)
{

  $.dm.ctrl = $.extend($.dm.coreCtrl, {
  
    init: function()
    {
      $('#dm_page div.dm_widget').trigger('dmWidgetLaunch');
    }
  
  });
  
})(jQuery);