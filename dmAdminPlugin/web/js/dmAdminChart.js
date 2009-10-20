(function($)
{

  $.dm.ctrl.add({
		
    init: function()
    {
      $('div.dm_charts', this.$dom).tabs({
				cache: true
			});
			
			$('a.dm_chart_link.selected').trigger('click');
    }
    
  });
  
})(jQuery);