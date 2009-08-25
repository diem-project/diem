(function($) {
  
  $.widget('ui.dmCodeArea', $.extend($.ui.dialog, {
		
		_init: function()
		{
			$.ui.dialog.prototype._init.apply(this, arguments);
			
			this.uiDialog.css('position', 'fixed');
		},
		
		_position: function(pos)
		{
			var wnd = $(window),
	      pTop = 0, pLeft = 0,
	      minTop = pTop;
	
	    if ($.inArray(pos, ['center','top','right','bottom','left']) >= 0) {
	      pos = [
	        pos == 'right' || pos == 'left' ? pos : 'center',
	        pos == 'top' || pos == 'bottom' ? pos : 'middle'
	      ];
	    }
	    if (pos.constructor != Array) {
	      pos = ['center', 'middle'];
	    }
	    if (pos[0].constructor == Number) {
	      pLeft += pos[0];
	    } else {
	      switch (pos[0]) {
	        case 'left':
	          pLeft += 0;
	          break;
	        case 'right':
	          pLeft += wnd.width() - this.uiDialog.outerWidth();
	          break;
	        default:
	        case 'center':
	          pLeft += (wnd.width() - this.uiDialog.outerWidth()) / 2;
	      }
	    }
	    if (pos[1].constructor == Number) {
	      pTop += pos[1];
	    } else {
	      switch (pos[1]) {
	        case 'top':
	          pTop += 0;
	          break;
	        case 'bottom':
	          pTop += wnd.height() - this.uiDialog.outerHeight();
	          break;
	        default:
	        case 'middle':
	          pTop += (wnd.height() - this.uiDialog.outerHeight()) / 2;
	      }
	    }
	
	    // prevent the dialog from being too high (make sure the titlebar
	    // is accessible)
	    pTop = Math.max(pTop, minTop);
	    $.dbg(pos, [pTop, pLeft]);
	    this.uiDialog.css({top: pTop, left: pLeft});
		}
	}));

}(jQuery);