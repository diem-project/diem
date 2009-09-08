(function($)
{

  $.widget('ui.dmAdminConfigPanel', {
  
    _init: function()
    {
      var self = this;
			
			self.element.tabs({
				
			});
      
    }
    
  });
	
	if ($configPanel = $('div.dm_config_panel').orNot())
	{
		$configPanel.dmAdminConfigPanel();
	}
  
})(jQuery);
