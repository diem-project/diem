(function($) {
  
var logs = new Array();
  
$.each(['request', 'event'], function()
{
  logs[this] = {
    hash: '',
    $wrapper: $('div.'+this+'_log')
  };

  logs[this].$wrapper.find('div.dm_box_inner').height(200);
});

setTimeout(refresh, 300);
	
function refresh()
{
  $.ajax({
    dataType: 'json',
    url:   $.dm.ctrl.getHref('+/dmLog/refresh'),
    data:  {
      rh:       logs.request.hash,
      eh:       logs.event.hash,
      dm_nolog: 1
    },
    success: function(data)
    {
      $.each(['request', 'event'], function()
      {
        if (data[this])
        {
          var current = this;
          logs[current].$wrapper.find('div.dm_box_inner').block();
          setTimeout(function() {
            logs[current].$wrapper.find('tbody').html(data[current].html).end().find('div.dm_box_inner').height('auto').unblock();
            logs[current].hash = data[current].hash;
          }, 200);
        }
      });
      setTimeout(refresh, 3000);
    },
    error: function()
    {
      setTimeout(refresh, 5000);
    }
  })
}

})(jQuery);