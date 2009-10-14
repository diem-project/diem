(function($) {
  
$.dm.ctrl.add($.dm.adminLogs = {
	
	init: function()
	{
		$.each(['user', 'action'], function(){
			$.dm.adminLogs[this] = {
				hash: '',
				$wrapper: $('div.'+this+'_log')
			}
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
	          $.dm.adminLogs[this].$wrapper.addClass('change').find('tbody').html(data[this].html);
	          $.dm.adminLogs[this].hash = data[this].hash;
	        }
	        else
	        {
	          $.dm.adminLogs[this].$wrapper.removeClass('change');
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