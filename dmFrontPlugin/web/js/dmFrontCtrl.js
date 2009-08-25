(function($)
{

  $.dm.ctrl = $.extend($.dm.coreCtrl, {
  
    init: function()
    {
      this.launchControllers($('#dm_page'));
    }
  
  });
  
})(jQuery);