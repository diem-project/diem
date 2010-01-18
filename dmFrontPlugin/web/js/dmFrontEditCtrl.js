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
      
      this.hotkeys();
    },
    
    hotkeys: function()
    {
      $("#dm_page").click(function(e)
      {
        if (e.ctrlKey) 
        {
          e.stopPropagation();
          if ($widget = $('#dm_page').dmPage('getWidgetByPos', e.pageX, e.pageY)) 
          {
            $widget.dmWidget('openEditDialog');
          }
          return false;
        }
      });
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