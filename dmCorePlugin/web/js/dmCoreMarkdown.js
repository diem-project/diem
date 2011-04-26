(function($)
{

  $.widget('ui.dmMarkdown', 
  {
    _init: function()
    {
      this.markitup();

      this.translateControls();

      this.selection = null;

      this.drop();

      this.saveSelection();
    },
  
  saveSelection: function()
  {
    var self = this;

    $('#dm_page_bar_toggler, #dm_media_bar_toggler').bind('click', function()
    {
      try
      {
        self.selection = self.element.getSelection();
      }
      catch(e){}
    });
  },
  
  getSelection: function()
  {
    return (this.selection && this.selection.length) ? this.selection : this.element.getSelection();
  },
  
  markitup: function()
  {
    this.element.markItUp(dmMarkitupMarkdown);
  },

  translateControls: function()
  {
    if($.dm.ctrl.options.culture == "en")
    {
      return;
    }
    
    var self = this;

    setTimeout(function()
    {
      $.ajax({
        url:      $.dm.ctrl.getHref('+/dmCore/getMarkdownTranslations'),
        data:     { culture: $.dm.ctrl.options.culture },
        cache:    true,
        dataType: 'json',
        success:  function(translations)
        {
          var messages = new Array();
          for (var i in translations)
          {
            messages.push(i);
          }

          self.element.parent().parent().find('div.markItUpHeader a').each(function()
          {
            $(this).attr('title', $(this).tipsyTitle().replace(new RegExp(messages.join("|"), "g"), function(message)
            {
              return translations[message];
            }));
          });
        }
      });
    }, 400);
  },
  
  drop: function()
  {
    var self = this;
   
    self.element.droppable({
      accept:       '#dm_page_bar li > a, #dm_media_bar li.file',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      //          tolerance:    'touch',
      drop: function(e, ui)
      {
        var selection = self.getSelection(),
        linkText = selection.text || $.trim(ui.draggable.find('span.ws').remove().end().text()),
        scrollTop = self.element.scrollTop();
     
        if (ui.draggable.hasClass('file'))
        {
          var mediaId = ui.draggable.attr('id').replace(/dmm/, '');
          
          if(ui.draggable.hasClass('image'))
          {
            var placeholder = "![](media:"+mediaId+")";
          }
          else
          {
            var placeholder = "["+linkText+"](media:"+mediaId+")";
          }
        }
        else
        {
          var placeholder = "["+linkText+"](page:"+ui.draggable.attr('data-page-id')+")";
        }
          
        if (selection)
        {
          var val = self.element.val();
          self.element.val(val.substr(0, selection.start) + placeholder + val.substr(selection.end, val.length));
        }
        else
        {
          self.element.val($elem.val()+placeholder);
        }
          
        self.element.scrollTop(scrollTop);
      }
    });
  }
});
  
})(jQuery);