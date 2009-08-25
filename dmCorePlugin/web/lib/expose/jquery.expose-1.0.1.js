/**
 * jquery.expose 1.0.1 - Make HTML elements stand out
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/expose.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : June 2008
 * Version : 1.0.1 - Wed Apr 15 2009 08:37:07 GMT-0000 (GMT+00:00)
 */
(function($) { 	

	function fireEvent(opts, name, self) {
		var fn = opts[name];
		
		if ($.isFunction(fn)) {
			
			try {  
				return fn.call(self);
				
			} catch (error) {
				if (opts.alert) {
					alert("Error calling expose." + name + ": " + error);
				} else {
					throw error;		
				}
				return false;
			} 			
		}
		return true;			
	}
	
	// mask instance (singleton)
	var mask = null;	
	

	// exposed elements
	var exposed, conf = null;
	var origIndex = 0;
	
	// global methods
	$.expose = {		
		
		getVersion: function() {
			return [1, 0, 0];	
		},
		
		getMask: function() {
			return mask;	
		},
		
		getExposed: function() {
			return exposed;	
		},
		
		getConf: function() {
			return conf;	
		},		
		
		isLoaded: function() {
			return mask && mask.is(":visible");	
		},
		
		load: function(els, opts) { 
			
			// already loaded ?
			if (this.isLoaded()) { return this;	}

			if (els) {
				exposed = els;
				origIndex = exposed.eq(0).css("zIndex");
				conf = opts;					
			} else {
				els = exposed;
				opts = conf;
			} 

			if (!els || !els.length) { return this; }
				
			// setup mask if not already done
			if (!mask) {
	
				mask = $('<div id="' + opts.maskId + '"></div>').css({				
					position:'absolute', 
					top:0, 
					left:0,
					width:'100%',
					height:$(document).height(),
					display:'none',
					opacity: 0,					 		
					zIndex:opts.zIndex	
				});
						
				
				$("body").append(mask);
				
				
				// esc button closes all instances
				$(document).bind("keypress.unexpose", function(evt) {
					if (evt.keyCode == 27) {
						$.expose.close();	
					}		
				});			
				
				// clicking on the mask closes all
				if (opts.closeOnClick) {
					mask.bind("click.unexpose", function()  {
						$.expose.close();		
					});					
				} 
			}

			
			// onBeforeLoad
			if (fireEvent(opts, "onBeforeLoad", this) === false) {
				return this;	
			}				
			
			// make sure element is positioned absolutely or relatively
			$.each(els, function() {
				var el = $(this);
				if (!/relative|absolute/i.test(el.css("position"))) {
					el.css("position", "relative");		
				}					
			});
		 
			// make elements sit on top of the mask
			els.css({zIndex:opts.zIndex + 1});				
			 

			// background color of the mask
			if (opts.color) {
				mask.css("backgroundColor", opts.color);	
			} 
			
			// reveal mask
			if (!this.isLoaded()) {					
				mask.css({opacity: 0, display: 'block'}).fadeTo(opts.loadSpeed, opts.opacity, function()  {
					fireEvent(opts, "onLoad", $.expose);  						
				});					
			}

			return this;
		}, 
		
		
		close: function() {
			
			var self = this;
			
			if (!this.isLoaded()) { return self; }   
			
			if (fireEvent(conf, "onBeforeClose", self) === false) {
				return self;   
			} 
			
			mask.fadeOut(conf.closeSpeed, function() {          
				exposed.css({zIndex: $.browser.msie ? origIndex : null});
				fireEvent(conf, "onClose", self);               
			});        
		}
		
	};
	
	// jQuery plugin initialization
	$.prototype.expose = function(conf) {

		// no elements to expose
		if (!this.length)  {
			return this;	
		}
		
		var opts = {
			/*
			CALLBACKS: 
			 - onBeforeLoad 
			 - onLoad
			 - onBeforeClose 
			 - onClose 
			*/		
			alert: true,

			// mask settings
			maskId: 'exposeMask',
			loadSpeed: 'slow',
			closeSpeed: 'fast',
			closeOnClick: true,
			
			// css settings
			zIndex: 9998,
			opacity: 0.8,
			color: '#333'
		};
		
		if (typeof conf == 'string') {
			conf = {color: conf};
		}
		
		$.extend(opts, conf);
		
		// call expose function		
		$.expose.load(this, opts);
		
		// return jQuery object
		return this;
		
	}; 


})(jQuery);