$.fn.panView = function(width,height) {
	return this.each(function(){
		
		var panViewWidthType = 'static';
		var panViewHeightType = 'static';
		var panId = this.id + "Pan";
		var panMaskId = this.id + "PanMask";
		var mouseState = 'up';
		var mouseStartX = 0;
		var mouseStartY = 0;
		var mouseDropX = 0;
		var mouseDropY = 0;
		var cssWidth;
		var cssHeight;
		var eThis = this; 
		// show image
		$(this).css('display','block');

		// check arguments
		if(width == 'auto') {		
			width = (this.clientWidth < document.body.clientWidth) ? this.clientWidth : document.body.clientWidth;
			panViewWidthType = 'dynamic';
		}
		if(height == 'auto') {
			height = (this.clientHeight < document.body.clientHeight) ? this.clientHeight : document.body.clientHeight;
			panViewHeightType = 'dynamic';
		}
	
		var bounds = new GetElementBounds(eThis);

		function MouseEvent(e) {
			this.e = e ? e : window.event; 
			this.source = e.target ? e.target : e.srcElement;
			this.x = this.e.pageX ? this.e.pageX : this.e.clientX;
			this.y = this.e.pageY ? this.e.pageY : this.e.clientY;
			if(window.event) {
				this.x = (document.body.scrollLeft) ? this.x + document.body.scrollLeft : this.x;
				this.y = (document.body.scrollTop) ? this.y + document.body.scrollTop : this.y;
			}
		}

		function GetElementBounds(o) {
			this.width = o.clientWidth;
			this.height = o.clientHeight;
			this.minX = (width - this.width);
			this.minY = (height - this.height);
			this.maxX = 0;
			this.maxY = 0;
		}
		
		$(this).wrap('<div id="' + panId + '" style="width:' + width + 'px; height:' + height + 'px; overflow:hidden; position: relative;"><div id="' + panMaskId + '" style="left:0px;top:0px;position:relative;"></div></div>');
		
		$(document).resize(function(e) {
			if(panViewWidthType == 'dynamic') {
				width = (this.clientWidth < document.body.clientWidth) ? this.clientWidth : document.body.clientWidth;
				$('div#' + panId).css('width',width + 'px');
			}
			if(panViewHeightType == 'dynamic') {
				height = (this.clientHeight < document.body.clientHeight) ? this.clientHeight : document.body.clientHeight;
				$('div#' + panId).css('height',height + 'px');
			}
			if(panViewWidthType == 'dynamic' || panViewHeightType == 'dynamic') {
				bounds = null;
				bounds = new GetElementBounds(eThis);
				
				mouseStartX = 0;
				mouseStartY = 0;
				mouseDropX = 0;
				mouseDropY = 0;
				
				$('div#' + panMaskId).css('left', 0 + 'px').css('top',0 + 'px');

			}

		});

		$('div#' + panId).mousedown(function(e) {
			
			mouseState = 'down';
			
			var me = new MouseEvent(e);
			
			mouseStartX = me.x;
			mouseStartY = me.y;	
		
			me = null;
			
			$(this).css('cursor','move');

			return false
		});

		$('div#' + panId).mouseup(function(e) {

			mouseState = 'up';
			
			mouseDropX = parseInt($('div#' + panMaskId).get(0).style.left);
			mouseDropY = parseInt($('div#' + panMaskId).get(0).style.top);

			$(this).css('cursor','default');

		});

		$(document.body).mousemove(function(e) {

			var me = new MouseEvent(e);
			
			var ePan = $('div#' + panId).get(0);
		

			// simple bound check.
			if(me.x < ePan.offsetLeft || me.x > (ePan.offsetLeft + width) || me.y < ePan.offsetTop || me.y > (ePan.offsetTop + height)) {
				mouseState = 'up';
			}
			
			if(mouseState == 'down') {
			
				
				
				var iLeft = mouseDropX - (mouseStartX - me.x);
				var iTop = mouseDropY - (mouseStartY - me.y);

				if(iLeft < bounds.minX) {
					iLeft = bounds.minX;
				} else if(iLeft > bounds.maxX) {
					iLeft = bounds.maxX;
				}
				
				if(iTop < bounds.minY) {
					iTop = bounds.minY;
				} else if(iTop > bounds.maxY) {
					iTop = bounds.maxY;
				}

				$('div#' + panMaskId).css('left', iLeft + 'px').css('top',iTop + 'px');	
			}
			
			me = null;

			return false
		});

	});
};