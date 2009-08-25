(function($) {
  
$.widget('ui.dmFrontForm', $.extend({}, $.dm.coreForm, {

  _init : function()
	{
		var self = this;
		
		this.form = $('form:first', this.element);
		
		this.linkDroppable();
		
		this.element.bindKey('Ctrl+s', function() {
			self.form.submit();
			return false;
		});
  }
  
}));

})(jQuery);