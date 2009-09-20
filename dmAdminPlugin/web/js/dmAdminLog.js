(function($) {
  

$.widget('ui.dmAdminLog', {
	
  _init : function()
  {
		var self = this;
		
    self.delay  = self.element.metadata().delay;
    self.url    = self.element.metadata().refresh_url;
		self.$container = self.element.find('tbody');
		self.hash   = null;
		
    setTimeout(function() { self.element.dmAdminLog('refresh');}, 200);
  },
	
	refresh: function()
	{
		var self = this;
		$.ajax({
			url:     self.url,
			data: { hash: self.hash },
			success: function(data)
			{
				if (data != '-') 
				{
          var parts = data.split(/\_\_DM\_SPLIT\_\_/);
					self.$container.html(parts[0]);
					self.hash = parts[1];
				}
				setTimeout(function() { self.element.dmAdminLog('refresh');}, self.delay);
			}
		});
	}
  
});

$.extend($.ui.dmAdminLog, {
  getter: "refresh"
});

$.dm.ctrl.add({ init: function() { $('div.log').dmAdminLog(); }});

})(jQuery);