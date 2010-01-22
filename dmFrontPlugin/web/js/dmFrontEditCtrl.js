(function($)
{

  $.dm.ctrl = $.extend($.dm.coreEditCtrl, $.dm.ctrl, {
  
    init: function()
    {
      var $page = $('#dm_page').dmPage();

      this.launchControllers($page);

      $page.find('div.dm_widget').trigger('dmWidgetLaunch');
			
      this.bars();
      
      this.liveEvents();
    },
    
    bars: function()
    {
      $('#dm_page_bar').dmFrontPageBar();
      
      if ($mediaBar = $('#dm_media_bar').orNot()) 
      {
        $mediaBar.dmFrontMediaBar();
      }
      
      $('#dm_tool_bar').dmFrontToolBar();
    }
  
  });
  
})(jQuery);