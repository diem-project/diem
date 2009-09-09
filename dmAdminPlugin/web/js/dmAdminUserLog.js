(function($) {
  
$.dm.ctrl.add($.dm.user_log = {

  delay:      $('div.user_log').metadata().delay,
	url:        $('div.user_log').metadata().refresh_url,
	$container: $('div.user_log tbody'),
	hash:       null,

  init: function()
  {
    setTimeout(this.refresh, 200);
  },
	
	refresh: function()
	{
		$.ajax({
			url:     $.dm.user_log.url,
			data: { hash: $.dm.user_log.hash },
			success: function(data)
			{
				if (data != '-') 
				{
          var parts = data.split(/\_\_DM\_SPLIT\_\_/);
					$.dm.user_log.$container.html(parts[0]);
					$.dm.user_log.hash = parts[1];
				}
				setTimeout($.dm.user_log.refresh, $.dm.user_log.delay);
			}
		});
	}
  
});

})(jQuery);