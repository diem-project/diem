(function($)
{

  $.dm.coreCtrl = {
    
    getHref: function(action)
    {
      return this.options.script_name + action;
    }

  };
 
 $(function()
 {
   $.dm.ctrl.options = $.extend($.dm.defaults, dm_configuration);
   $.dm.ctrl.init();
 });
  
})(jQuery);