(function($) {
  
$.dm.ctrl.add($.dm.adminLogs = {
	
	userHash: '',
	$userContainer: $('div.user_log tbody'),
	actionHash: '',
	$actionContainer: $('div.action_log tbody'),
	
	init: function()
	{
	  setTimeout($.dm.adminLogs.refresh, 200);
  },
	
	refresh: function()
	{
		$.ajax({
			url:   $.dm.ctrl.getHref('+/dmAdmin/refreshLogs'),
			data:  {
				user_hash: $.dm.adminLogs.userHash,
				action_hash: $.dm.adminLogs.actionHash
			},
			success: function(data) {
				var parts = data.split(/\_\_DM\_SPLIT\_\_/);
        if (parts[0] != '-')
        {
          $.dm.adminLogs.$userContainer.html(parts[0]);
					$.dm.adminLogs.userHash = parts[1];
        }
        if (parts[2] != '-')
        {
          $.dm.adminLogs.$actionContainer.html(parts[2]);
          $.dm.adminLogs.actionHash = parts[3];
        }
				setTimeout($.dm.adminLogs.refresh, 1000);
			},
			error: function() {
				setTimeout($.dm.adminLogs.refresh, 2000);
			}
		})
	}
});

})(jQuery);