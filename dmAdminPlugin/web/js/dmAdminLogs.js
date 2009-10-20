(function($) {
  
$.dm.ctrl.add($.dm.adminLogs = {
	
	init: function()
	{
		$.each(['user', 'action'], function(){
			$.dm.adminLogs[this] = {
				hash: '',
				$wrapper: $('div.'+this+'_log')
			};
			
			$.dm.adminLogs[this].$wrapper.find('div.dm_box_inner').height(200);
		});
		
	  setTimeout($.dm.adminLogs.refresh, 500);
  },
	
	refresh: function()
	{
		$.ajax({
			dataType: 'json',
			url:   $.dm.ctrl.getHref('+/dmAdmin/refreshLogs'),
			data:  {
				user_hash: $.dm.adminLogs.user.hash,
				action_hash: $.dm.adminLogs.action.hash
			},
			success: function(data) {
				$.each(['user', 'action'], function(){
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