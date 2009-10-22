(function($)
{

  $.dm.ctrl.add({
		
    init: function()
    {
      var $tabs = $('div.dm_charts', this.$dom);
      
      $tabs.tabs($.extend({
        cache: true,
        select: function() {
          $tabs.block();
        },
        show: function() {
          $tabs.unblock();
        }
      }, $tabs.metadata()));
    }
    
  });
  
})(jQuery);