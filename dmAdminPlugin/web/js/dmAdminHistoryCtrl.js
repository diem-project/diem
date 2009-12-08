(function($) {
  
$.dm.historyCtrl = {

  init: function()
  {
    this.$ = $("div.dm_history");
    
    this.metadata = this.$.metadata();
    
		this.tabs();
  },
	
	tabs: function()
	{
		var self = this;
		
		self.$.tabs({
			
		});
	}
};

$.dm.ctrl.add($.dm.historyCtrl);

})(jQuery);