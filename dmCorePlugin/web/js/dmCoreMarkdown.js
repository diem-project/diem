(function($)
{

  $.widget('ui.dmMarkdown', 
  {
    _init: function()
    {
      this.markitup();

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
  
  drop: function()
  {
    var self = this;
   
    self.element.droppable({
      accept:       '#dm_page_bar li > a, #dm_media_bar li.file.image',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      //          tolerance:    'touch',
      drop: function(e, ui)
      {
        var selection = self.getSelection(),
        linkText = selection.text || $.trim(ui.draggable.text()),
        scrollTop = self.element.scrollTop();
     
        if (ui.draggable.hasClass('file'))
        {
          var type = "media";
          var placeholder = "!["+linkText+"]("+type+":"+ui.draggable.attr('id').replace(/dmm/, '')+")"
        }
        else
        {
          var type = "page";
          var placeholder = "["+linkText+"]("+type+":"+ui.draggable.attr('data-page-id')+")"
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