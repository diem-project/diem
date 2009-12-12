(function($) {
  
$.widget('ui.dmFrontForm', $.extend({}, $.dm.coreForm, {

  _init : function()
	{
		var self = this;
		
		this.form = $('form:first', this.element);
		
    this.markdown();
		
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
  
  markdown: function()
  {
    var self = this;
    
    $('textarea.dm_markdown', self.form).each(function()
		{
      $(this).dmMarkdown().resizable({handles: 's'}).parent().css('position', 'relative');
    });
  }
  
}));

$.extend($.ui.dmFrontForm, {
  getter: "linkDroppable"
});

})(jQuery);