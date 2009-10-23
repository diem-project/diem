(function($) {
  
$.dm.ctrl.add($.dm.adminLogs = {
	
	init: function()
	{
		$.each(['request', 'event'], function(){
			$.dm.adminLogs[this] = {
				hash: '',
				$wrapper: $('div.'+this+'_log')
			};
			
			$.dm.adminLogs[this].$wrapper.find('div.dm_box_inner').height(200);
		});
		
	  setTimeout($.dm.adminLogs.refresh, 300);
  },
	
	refresh: function()
	{
		$.ajax({
			dataType: 'json',
			url:   $.dm.ctrl.getHref('+/dmLog/refresh'),
			data:  {
				rh: $.dm.adminLogs.request.hash,
				eh: $.dm.adminLogs.event.hash
			},
			success: function(data) {
				$.each(['request', 'event'], function(){
					if (data[this])
	        {
						var current = this;
            $.dm.adminLogs[current].$wrapper.find('div.dm_box_inner').block();
						setTimeout(function() {
		          $.dm.adminLogs[current].$wrapper.find('tbody').html(data[current].html).end().find('div.dm_box_inner').height('auto').unblock();
		          $.dm.adminLogs[current].hash = data[current].hash;
						}, 200);
	        }
				});
				setTimeout($.dm.adminLogs.refresh, 3000);
			},
			error: function() {
				setTimeout($.dm.adminLogs.refresh, 5000);
			}
		})
	}
});

})(jQuery);