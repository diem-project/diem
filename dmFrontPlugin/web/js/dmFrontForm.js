(function($) {
  
$.widget('ui.dmFrontForm', {

  _init : function()
  {
    var self = this;
    
    this.form = $('form:first', this.element);
    
    this.markdown();
    this.droppableInput();
    this.hotKeys();
  },

  droppableInput: function()
  {
    $('input.dm_link_droppable, .dm_link_droppable input', this.element).dmDroppableInput();
  },

  hotKeys: function()
  {
    var self = this;

    self.element.bindKey('Ctrl+s', function() {
      self.form.submit();
      return false;
    });
  },
  
  markdown: function()
  {
    var self = this;
    
    $('textarea.dm_markdown', self.form).each(function()
    {
      $(this).dmMarkdown();
    });
  }
  
});

})(jQuery);