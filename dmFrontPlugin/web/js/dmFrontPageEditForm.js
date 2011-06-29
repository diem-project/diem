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
        success: function(html)
        {
          if (html.substr(0, 7) == 'http://')
          {
            self.element.dialog('close');
            $('body').block();
            location.href = html;
            return;
          }
          self.element.html(html);
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
        $(this).parent().parent().find('> li.credentials')[$(this).prop('checked') ? 'show' : 'hide']();
      });
    }
    
  });
  
})(jQuery);
