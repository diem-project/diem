(function($) {
  
$.widget('ui.dmFrontForm', $.extend({}, $.dm.coreForm, {

  _init : function()
	{
		var self = this;
		
		this.form = $('form:first', this.element);
		
    this.markdown();
		
    this.linkDroppable();
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