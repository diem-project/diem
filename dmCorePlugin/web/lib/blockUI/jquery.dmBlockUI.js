(function($) {

  $.fn.extend({
		
		block: function()
		{
			return this.css('opacity', 0.5);
		},
		unblock: function()
		{
			return this.css('opacity', 1);
		}
	});
	
})(jQuery);