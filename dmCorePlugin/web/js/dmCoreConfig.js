(function($) {
  
$.dm = {
  defaults : {
    ajaxData: {
      dm_cpi:  dm_configuration.page_id || 0,
   // tell the server the xhr nature of the request. Usefull when uploading file
   dm_xhr:  1
    }
  },
  base : {}
};

// jQuery
$.ajaxSetup({
  global :  false,
  timeout : false,
  type :    "GET",
  cache :   false,
  data:     $.dm.defaults.ajaxData
});

// jQuery UI
if ($.datepicker)
{
  $.datepicker.setDefaults($.datepicker.regional[dm_configuration.culture]);
}

// jQuery plugins
if ($.blockUI)
{
 $.blockUI.defaults = $.extend($.blockUI.defaults, {
    css:        {},
    overlayCSS: {},
    message:    ' ',
  fadeIn:     0,
  fadeOut:    0
 });
}

// Performance: disable tipsy usage of $.metadata
if($.fn.tipsy)
{
  $.fn.tipsy.elementOptions = function(elem, options) {
    return options;
  };
  $.fn.tipsy.defaults.delayIn = 100;
}

})(jQuery);