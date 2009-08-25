(function($)
{

  $.widget('ui.dmFrontPageEditForm', {
  
    _init: function()
    {
      this.form();
    },
    
    form: function()
    {
      var self = this;
      
			self.element.dmFrontForm();
			
      self.$tabs = self.element.find('div.dm_page_edit').dmCoreTabForm({});
      
      self.$form = $('form', self.element).dmAjaxForm({
        beforeSubmit: function(data)
        {
          self.element.block();
        },
        success: function(data)
        {
          if (data.match(/\_\_DM\_SPLIT\_\_/))
          {
            self.element.dialog('close');
            $('body').block();
            location.href = data.split(/\_\_DM\_SPLIT\_\_/)[1];
            return;
          }
          self.element.html(data);
          self.form();
        }
      });
			
			var maxLengths = $.extend(self.$form.find('div.dm_seo_max_lengths').metadata(),
			{
				'module': 127,
				'action': 127
			});
			
			for(fieldName in maxLengths)
			{
				$('#dm_page_'+fieldName, self.$form).maxLength(maxLengths[fieldName]);
			}
    }
    
  });
  
})(jQuery);
