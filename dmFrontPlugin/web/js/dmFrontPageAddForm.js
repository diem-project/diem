(function($)
{

  $.widget('ui.dmFrontPageAddForm', {
  
    _init: function()
    {
      this.autoSlug = true;
      
      this.form();
    },
    
    form: function()
    {
      var self = this;
      
      self.element.dmFrontForm();
      
      self.$name = $('input#dm_page_front_new_form_name', self.element);
      
      self.$parent = $('select#dm_page_front_new_form_parent_id', self.element);
      
      self.$slug = $('input#dm_page_front_new_form_slug', self.element).attr('disabled', self.autoSlug);

      self.parentSlugs = $.parseJSON($('div.parent_slugs', self.element).text());
      self.transliteration = $.extend(
        $.parseJSON($('div.transliteration', self.element).text()),
        {
          '_': '-',
          ',': '-',
          ':': '-'
        }
      );
      self.transliterationSources = new Array();
      for (var i in self.transliteration)
      {
        self.transliterationSources.push(i);
      }
      
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
      
      self.$parent.bind('change', function()
      {
        self.changeSlug()
      });
      
      self.$name.bind('keyup', function()
      {
        self.changeSlug()
      });
      
      self.$slug.bind('keyup', function()
      {
        self.$slug.val(self.slugify(self.$slug.val()));
        self.autoSlug = false;
      });
      
      self.changeSlug();
    },
    
    changeSlug: function()
    {
      if (!this.autoSlug) 
      {
        return;
      }
      
      var self = this, parentSlug = self.parentSlugs[self.$parent.val()], name = self.$name.val();

      self.$slug.val(self.slugify(parentSlug ? parentSlug + '/' + name : name));
      
      if (self.$slug.attr('disabled') && name) 
      {
        self.$slug.attr('disabled', false);
      }
    },
    
    slugify: function(str)
    {
      var self = this;
      
      if(!str) return '';
      
      str = str.toLowerCase();

      str = str.replace(new RegExp(self.transliterationSources.join("|"), "g"), function(source)
      {
        return self.transliteration[source];
      });
      
      str = str
      .replace(/\s+|_|\,|;|:/g, '-')
      .replace(/[^a-zA-Z0-9-\/\.]/g, '')
      .replace(/-{2,}/g, '-');
      
      return str;
    }
    
  });
  
})(jQuery);
