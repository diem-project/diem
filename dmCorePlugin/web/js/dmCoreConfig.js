(function($) {
  
$.dm = {
  defaults : {
    ajaxData: {
      dm_cpi:  dm_configuration.page_id || 0,
      dm_xhr:  1
    }
  },
  base : {
    
  }
};

// configuration de jQuery
$.ajaxSetup({
  global : false,
  timeout : false,
  type : "GET",
  cache : false,
  data: $.dm.defaults.ajaxData
});

//Configuration de jQuery UI
if ($.datepicker)
{
  $.datepicker.regional[dm_configuration.culture];
}

if ($.blockUI)
{
  $.blockUI.defaults.css = {};
  $.blockUI.defaults.message = ' ';
}

})(jQuery);