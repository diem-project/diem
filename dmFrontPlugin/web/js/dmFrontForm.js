(function($) {
  
$.widget('ui.dmFrontForm', $.extend({}, $.dm.coreForm, {

  _init : function()
	{
		var self = this;
		
		this.form = $('form:first', this.element);
		
    this.markitup();
    this.linkDroppable();
    this.hotKeys();
  },
	
	hotKeys: function()
	{
		var self = this;
		
    self.element.bindKey('Ctrl+s', function() {
      self.form.submit();
      return false;
    });
	},
  
  markitup: function()
  {
    var self = this;
    
    $('textarea.dm_markdown', self.form).each(function()
		{
      $(this).markItUp(dmMarkitupMarkdown);
      $(this).resizable({handles: 's'});
    });
  }
  
}));

})(jQuery);