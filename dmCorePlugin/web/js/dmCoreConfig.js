(function($) {
  
$.dm = {
  defaults : {
    ajaxData: {
      dm_cpi:  dm_configuration.page_id || 0,
			// tell the server the xhr nature of the request. Usefull when uploading file
			dm_xhr:  1
    }
  },
  base : {
    
  }
};

// configuration de jQuery
$.ajaxSetup({
  global :  false,
  timeout : false,
  type :    "GET",
  cache :   false,
  data:     $.dm.defaults.ajaxData
});

//Configuration de jQuery UI
if ($.datepicker)
{
  $.datepicker.regional[dm_configuration.culture];
}

if ($.blockUI)
{
	$.blockUI.defaults = $.extend($.blockUI.defaults, {
    css:      {},
    message:  ' ',
		fadeIn:   0,
		fadeOut:  0
	});
}

})(jQuery);