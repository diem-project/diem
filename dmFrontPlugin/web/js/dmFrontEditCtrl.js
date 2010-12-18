(function($)
{

  $.dm.ctrl = $.extend($.dm.coreEditCtrl, $.dm.ctrl, {
  
    init: function()
    {
      $('#dm_page').dmPage().find('div.dm_widget').trigger('dmWidgetLaunch');
      
      $('#dm_page_bar').dmFrontPageBar();

      if ($mediaBar = $('#dm_media_bar').orNot())
      {
        $mediaBar.dmFrontMediaBar();
      }

      $('#dm_tool_bar').dmFrontToolBar();
      
      this.liveEvents();
    }
  
  });
  
})(jQuery);