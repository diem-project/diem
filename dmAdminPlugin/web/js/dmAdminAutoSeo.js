(function($)
{

  $.dm.ctrl.add({
    
    init: function()
    {
      this.metaPreview();
			
      this.variables();
			
			$('li.ui-state-default', this.$dom).hover(function() {
        $(this).addClass('ui-state-hover');
      },function() {
        $(this).removeClass('ui-state-hover');
      });
    },
		
		metaPreview: function()
		{
			var $form = $('form', this.$dom);
			
      $('li.dm_meta_preview', this.$dom).each(function(index) {
				$(this).height($('li.dm_form_element:eq('+index+')', $form).height());
      });
		},
		
		variables: function()
		{
			var $variables = $('div.dm_variables', this.$dom);
			
			$variables.find('>ul').accordion({
				collapsible: true,
				active: false,
			});
		}
    
  });
  
})(jQuery);