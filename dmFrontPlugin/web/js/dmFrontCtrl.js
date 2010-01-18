(function($)
{

  $.dm.ctrl = $.extend($.dm.coreCtrl, {
  
    init: function()
    {
      var $page = $('#dm_page');
      
      this.launchControllers($page);

      $page.find('div.dm_widget').trigger('dmWidgetLaunch');
    }
  
  });
  
})(jQuery);