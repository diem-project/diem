(function($)
{

  $.dm.ctrl = $.extend($.dm.coreEditCtrl, $.dm.ctrl, {
  
    init: function()
    {
      this.launchControllers($('#dm_page'));
			
      this.bars();
      
      this.flashMessages();
      
      this.liveEvents();
      
      this.page();
      
      this.hotkeys();
      
      this.test();
    },
    
    test: function()
    {
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
    
    page: function()
    {
      $('#dm_page').dmPage();
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