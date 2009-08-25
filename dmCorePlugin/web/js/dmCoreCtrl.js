(function($)
{

  $.dm.coreCtrl = {
  
    controllers: [],
    
    add: function(ctrl)
    {
      this.controllers.push(ctrl);
    },
    
    launchControllers: function($dom)
    {
      for (var i in this.controllers) 
      {
        this.controllers[i].$dom = $dom;
        this.controllers[i].options = this.options;
				this.controllers[i].init();
      }
    },
    
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