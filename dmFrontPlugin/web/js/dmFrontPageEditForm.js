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
				dataType: 'json',
        beforeSubmit: function(data)
        {
          self.element.block();
        },
        success: function(data)
        {
          if (data.type == 'redirect')
          {
            self.element.dialog('close');
            $('body').block();
            location.href = data.url;
            return;
          }
          self.element.html(data.html);
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

      self.$form.find('input#dm_page_front_edit_form_is_secure').bind('click', function() {
        $(this).parent().parent().find('> li.credentials')[$(this).attr('checked') ? 'show' : 'hide']();
      });
    }
    
  });
  
})(jQuery);
