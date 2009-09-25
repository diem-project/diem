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
			dataType: 'json',
			url:   $.dm.ctrl.getHref('+/dmAdmin/refreshLogs'),
			data:  {
				user_hash: $.dm.adminLogs.userHash,
				action_hash: $.dm.adminLogs.actionHash
			},
			success: function(data) {
        if (data.user)
        {
          $.dm.adminLogs.$userContainer.html(data.user.html);
					$.dm.adminLogs.userHash = data.user.hash;
        }
        if (data.action)
        {
          $.dm.adminLogs.$actionContainer.html(data.action.html);
          $.dm.adminLogs.actionHash = data.action.hash;
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