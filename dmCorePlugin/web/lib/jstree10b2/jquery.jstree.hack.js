/*
 * Diem hack: removed json plugin
 */

/*
BUGS:
	form issue - keypress / keyup in rename function
	create did not work if LI node has an empty UL inside (old bug maybe not applicable)
	firefox 2 - display:inline-block
	use map function when collecting IDs

FEATURES:
	drag'n'drop + method to cancel it (dragged with high z-index), make sure OK/CANCEL icon are with !important (so that custom icons are overlapped), or place the icon somewhere else
	localization - the loading text, maybe default title (maybe per type - override in types plugin)
	metadata plugin - do what??? maybe just provide support by every plugin?
	create_node (when creating in async - make sure contents are loaded first) /plugin?
	maybe use a class on the A nodes (to be able to style other links and not capture the event)
	add sorting plugin - attach to open/move
	$.ajax - use $.proxy

PLUGINS & THEMES:
	* contextmenu 
	  - do not open if no valid items, 
	  - maybe option to select node prior to clicking, 
	  - localization, 
	  - type based entries? 
	  - maybe use external plugin? 
	* themeroller, 
	* xml_flat, 
	* xml_nested, 
	* mvc plugin - a plugin exposing the JSON tree object, and calling refresh (MVC)
	* rollback - (get_state, set_state) - specify a list of functions to monitor ($.data will be lost - use clone), make steps back a setting
	REDO ALL THEMES
	maybe accept position param in json and sort by it - think of sorting all together (alphabetical even?)
	finish __destroy methods for all plugins so that they remove the classes/events they added
	IE6 support - single theme or plugin (hook to some events and additional classes)
	describe dependencies and onload/oninit check if the other plugins are included before the current one

TEST:
	multiple nodes to be initially open (async)
	unit testing? (maybe func by func - using each on .fn?)
	foreign drag'n'drop example

EXAMPLES & DOCS!
*/

/*
 * jsTree 1.0-beta
 * http://jstree.com/
 *
 * Copyright (c) 2009 Ivan Bozhanov (vakata.com)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Date: 2010-01-14
 */
// TODO: rethink wrapping of the functions - maybe not at document ready, but first core, then plugin by plugin
"use strict";
(function ($) {
	// private variables 
	var instances = [],			// instance array (used by $.jstree.reference/create/focused)
		focused_instance = -1,	// the index in the instance array of the currently focused instance
		plugins = {};			// list of included plugins

	// jQuery plugin wrapper
	// thank to jquery UI widget function
	$.fn.jstree = function (settings) {
		var isMethodCall = (typeof settings == 'string'), // is this a method call like $().jstree("open_node")
			args = Array.prototype.slice.call(arguments, 1), 
			returnValue = this;

			// extend settings and allow for multiple hashes and metadata
			if(!isMethodCall && $.meta) { args.push($.metadata.get(this).jstree); }
			settings = !isMethodCall && args.length ? $.extend.apply(null, [true, settings].concat(args)) : settings;
			// block calls to "private" methods
			if(isMethodCall && settings.substring(0, 1) == '_') { return returnValue; }

			// if a method call execute the method on all selected instances
			if(isMethodCall) {
				this.each(function() {
					var instance = instances[$.data(this, "jstree-instance-id")],
						methodValue = (instance && $.isFunction(instance[settings])) ? instance[settings].apply(instance, args) : instance;
						if(typeof methodValue !== "undefined") { returnValue = methodValue; return false; }
				});
			}
			else {
				this.each(function() {
					var instance_id = $.data(this, "jstree-instance-id");
					// if an instance already exists, destroy it first
					if(instance_id && instances[instance_id]) { instances[instance_id].destroy(); }
					// push a new empty object to the instances array
					instance_id = parseInt(instances.push({}),10) - 1;
					// store the jstree instance id to the container element
					$.data(this, "jstree-instance-id", instance_id);

					// TEMPORARY FIX
					var s = $.extend(true, {}, $.jstree.defaults, settings);
					if(settings.plugins) { s.plugins = settings.plugins; }

					// push the new object to the instances array (at the same time set the default classes to the container) and init
					instances[instance_id] = new $.jstree._instance(instance_id, $(this).addClass("jstree jstree-" + instance_id), s); 
					// init all activated plugins for this instance
					$.each(instances[instance_id].get_settings().plugins, function (i, val) { instances[instance_id].data[val] = {}; });
					$.each(instances[instance_id].get_settings().plugins, function (i, val) { plugins[val].__init.apply(instances[instance_id]); });
					// initialize the instance
					instances[instance_id].init();
				});
			}
		// return the jquery selection (or if it was a method call that returned a value - the returned value)
		return returnValue;
	};

	// object to store exposed functions and objects
	$.jstree			= {};
	// defaults are exposed, so that the developer can change defaults for all future instances
	$.jstree.defaults	= { animation : 500 };

	// "private" exposed functions - use with caution
	// gets the currently focused instance (used internally)
	$.jstree._focused	= function () { return instances[focused_instance] || null; };
	// get an instance by node, dom id, reference id
	$.jstree._reference	= function (needle) { 
		// get by instance id
		if(instances[needle]) { return instances[needle]; }
		// get by DOM (if still no luck - return null
		var o = $(needle); o = o.length ? o : $("#" + o); if(!o.length) { return null; }
		// traverse up the DOM for the tree parent
		o = (o.is(".jstree")) ? o.get(0) : o.parents(".jstree:eq(0)").get(0);
		o = $.data(o, "jstree-instance-id");
		return instances[o] || null; 
	};
	// the actual instance function (used internally)
	$.jstree._instance	= function (index, container, settings) { 
		// for plugins to store data in
		this.data = { core : {} };
		this.get_settings	= function () { return $.extend(true, {}, settings); };
		this.get_index		= function () { return index; };
		this.get_container	= function () { return container; };
		this._set_settings	= function (s) { 
			settings = $.extend(true, {}, settings, s);
		};
	};

	// the prototype to attach functions to (all core functions are listed here)
	$.jstree._fn = $.jstree._instance.prototype = {
		// initialize and destroy
		init	: function () { 
			var _this = this;
			this.set_focus();
			this.get_container().html("<ul><li class='jstree-last jstree-leaf'><ins>&nbsp;</ins><a class='jstree-loading' href='#'><ins class='icon'>&nbsp;</ins>Loading ...</a></li></ul>");
      this.data.core.li_height = this.get_container().find("ul li.jstree-closed, ul li.jstree-leaf").eq(0).height() || 18;
      this.load_node(-1, function () { _this.loaded(); });
		},
		destroy	: function () { 
			var i,
				n = this.get_index(), 
				c = this.get_container(),
				s = this.get_settings(),
				_this = this;

			$.each(s.plugins, function (i, val) {
				plugins[val].__destroy.apply(_this);
			});

			// set focus to another instance if this one is focused
			if(this.is_focused()) { 
				for(i in instances) { 
					if(instances.hasOwnProperty(i) && i != n) { 
						instances[i].set_focus(); 
						break; 
					} 
				}
			}
			$.removeData(c, "jstree-instance-id");
			// remove all traces of jstree in the DOM (only the ones set by the core, plugins should clean themselves)
			c.removeClass("jstree jstree-" + this.get_index() + " jstree-focused");
			// remove the actual data
			instances[n] = null;
			delete instances[n];
		},
		// Dummy function to fire after the first load (so that there is a jstree.loaded event)
		loaded	: function () { },

		// deal with focus
		set_focus	: function () { 
			var f = $.jstree._focused();
			if(f && f !== this) {
				f.get_container().removeClass("jstree-focused"); 
			}
			if(f !== this) {
				this.get_container().addClass("jstree-focused"); 
				focused_instance = this.get_index(); 
			}
		},
		is_focused	: function () { 
			return focused_instance == this.get_index(); 
		},

		// traverse
		_get_node		: function (obj) { var $obj = $(obj, this.get_container()); return $obj.is(".jstree") || obj == -1 ? -1 : $obj.closest("li", this.get_container()); },
		_get_next		: function (obj, strict) {
			obj = this._get_node(obj);
			if(!obj.length) { return false; }
			if(strict) { return (obj.nextAll("li").size() > 0) ? obj.nextAll("li:eq(0)") : false; }

			if(obj.hasClass("jstree-open")) { return obj.find("li:eq(0)"); }
			else if(obj.nextAll("li").size() > 0) { return obj.nextAll("li:eq(0)"); }
			else { return obj.parentsUntil(this.get_container(),"li").next("li").eq(0); }
		},
		_get_prev		: function (obj, strict) {
			obj = this._get_node(obj);
			if(!obj.length) { return false; }
			if(strict) { return (obj.prevAll("li").length > 0) ? obj.prevAll("li:eq(0)") : false; }

			if(obj.prev("li").length) {
				obj = obj.prev("li").eq(0);
				while(obj.hasClass("jstree-open")) { obj = obj.children("ul:eq(0)").children("li:last"); }
				return obj;
			}
			else { var o = obj.parentsUntil(this.get_container(),"li:eq(0)"); return o.length ? o : false; }
		},
		_get_parent		: function (obj) {
			obj = this._get_node(obj);
			if(obj == -1 || !obj.length) { return false; }
			var o = obj.parentsUntil(this.get_container(), "li:eq(0)");
			return o.length ? o : -1;
		},
		_get_children	: function (obj) {
			obj = this._get_node(obj);
			if(obj === -1) { return this.get_container().children("ul:eq(0)").children("li"); }
			if(!obj.length) { return false; }
			return obj.children("ul:eq(0)").children("li");
		},
		get_path		: function (obj, id_mode) {
			var p = [],
				_this = this;
			obj = this._get_node(obj);
			if(obj === -1 || !obj || !obj.length) { return false; }
			obj.parentsUntil(this.get_container(), "li").each(function () {
				p.push( id_mode ? this.id : _this.get_text(this) );
			});
			p.reverse();
			p.push( id_mode ? obj.attr("id") : this.get_text(obj) );
			return p;
		},

		// deal with text
		get_text	: function (obj) {
			obj = this._get_node(obj);
			if(!obj.size()) { return false; }
			obj = obj.children("a:eq(0)");
			obj = obj.contents().filter(function() { return this.nodeType == 3; })[0];
			return obj.nodeValue;
		},
		set_text	: function (obj, val) {
			obj = this._get_node(obj);
			if(!obj.size()) { return false; }
			obj = obj.children("a:eq(0)");
			obj = obj.contents().filter(function() { return this.nodeType == 3; })[0];
			return (obj.nodeValue = val);
		},

		// open/close
		open_node	: function (obj, callback) {
			obj = this._get_node(obj);
			if(!obj.length) { return false; }
			if(!this.is_loaded(obj)) {
				var _this = this;
				obj.children("a").addClass("jstree-loading");
				this.load_node(obj, function () { _this.open_node(obj, callback); }, callback);
				this.open_node.supress_callback = true;
			}
			else {
				this.open_node.supress_callback = false;
				if(this.get_settings().animation) { obj.children("ul").css("display","none"); }
				obj.removeClass("jstree-closed").addClass("jstree-open").children("a").removeClass("jstree-loading");
				if(this.get_settings().animation) { obj.children("ul").slideDown(this.get_settings().animation, function () { this.style.display = ""; }); }
				if(callback) { callback.call(); }
			}
		},
		close_node	: function (obj) {
			obj = this._get_node(obj);
			if(!obj.length) { return false; }
			if(this.get_settings().animation) { obj.children("ul").attr("style","display:block !important"); }
			obj.removeClass("jstree-open").addClass("jstree-closed");
			if(this.get_settings().animation) { obj.children("ul").slideUp(this.get_settings().animation, function () { this.style.display = ""; }); }
		},
		toggle_node	: function (obj) {
			obj = this._get_node(obj);
			if(obj.hasClass("jstree-closed")) { return this.open_node(obj); }
			if(obj.hasClass("jstree-open")) { return this.close_node(obj); }
		},
		open_all	: function (obj, original_obj) {
			obj = obj ? this._get_node(obj) : this.get_container();
			if(original_obj) { 
				obj = obj.find("li.jstree-closed");
			}
			else {
				original_obj = obj;
				if(obj.is(".jstree-closed")) { obj = obj.find("li.jstree-closed").andSelf(); }
				else { obj = obj.find("li.jstree-closed"); }
			}
			var _this = this;
			obj.each(function () { 
				var __this = this; 
				_this.open_node(this, function() { _this.open_all(__this, original_obj); });
			});
			// so that callback is fired AFTER all tabs are open
			this.open_all.supress_callback = (!original_obj || original_obj.find('li.jstree-closed').length > 0);
		},
		close_all	: function (obj) {
			var _this = this;
			obj = obj ? this._get_node(obj) : this.get_container();
			obj.find("li.jstree-open").andSelf().each(function () { _this.close_node(this); });
		},

		clean_node	: function(obj) {
			obj = obj && obj != -1 ? $(obj) : this.get_container();
			obj = obj.is("li") ? obj.find("li").andSelf() : obj.find("li");
			obj.removeClass("jstree-last")
				.filter("li:last-child").addClass("jstree-last").end()
				.filter(":has(ul)")
					.not(".jstree-open").removeClass("jstree-leaf").addClass("jstree-closed");
			obj.not(".jstree-open, .jstree-closed").addClass("jstree-leaf");
		},

		// rollback
		get_rollback : function() { 
			this.get_rollback.supress_callback = true;
			return { i : this.get_index(), h : this.get_container().children("ul").clone(true), d : this.data }; 
		},

		// Dummy functions to be overwritten by any datastore plugin included
		load_node	: function (obj, s_call, e_call) { },
		is_loaded	: function (obj) { return true; }
	};

	// plugin functionality
	// a setting to store all active plugins
	$.jstree.defaults.plugins = [];
	// function to add plugins by name and values
	$.jstree.plugin = function (pname, pdata) {
		// the default empty plugin
		pdata = $.extend(true, {}, {
			__init		: function () { }, 
			__destroy	: function () { },
			_fn			: { },
			defaults	: false
		}, pdata);
		// push the plugin's name in the loaded plugins array
		plugins[pname] = pdata;
		// set the origin on every function and also - the overwritten function (if there is one)
		$.each(pdata._fn, function (i, val) { 
			val.plugin = pname; 
			val.old = $.jstree._fn[i];
		});
		// extend the _instance functions by those of the plugin
		$.extend($.jstree._fn, pdata._fn);
		// extend the defaults
		$.jstree.defaults[pname] = pdata.defaults;
	};

	var rollback = ["open_node","close_node"];
	$.jstree.rollback = function (rb) {
		if(rb) {
			if(!$.isArray(rb)) { rb = [ rb ]; }
			$.each(rb, function (i, val) {
				instances[val.i].get_container().empty().append(val.h);
				instances[val.i].data = val.d;
			});
		}
	};
	$.jstree.register_rollback = function (i) {
		i = i.split(" ");
		$.each(i, function(j,v) { rollback.push(v); });
	};

	// wrap functions (for plugins and events)
	$(function () {
		$.each($.jstree._fn, function (i, val) {
			if(!$.isFunction(val)) { return true; }
			var of = val, 
				rb = false;
			$.jstree._fn[i] = function () {

				var is_callable = false,
					ret;
				do {
					if(!val.plugin || (val.plugin && $.inArray(val.plugin, this.get_settings().plugins) != -1)) {
						is_callable = true;
						break;
					}
					val = val.old;
				} while(val);
				if(!is_callable) { return; }

				if(!this[i].supress_callback && i.substring(0, 1) != '_') {
					if($.inArray(i, rollback) !== -1) { rb = this.get_rollback(); }
					ret = val.apply(this, arguments);
					this.get_container().triggerHandler('jstree.' + i, {
						'arg'	: Array.prototype.slice.call(arguments),
						'ret'	: ret,
						'rb'	: rb
					});
				}
				else {
					ret = val.apply(this, arguments);
				}
				val = of;
				return ret;
			};
		});
	});

	// css functions - used internally
	$.jstree._css = {
		get_css : function(rule_name, delete_flag, sheet) {
			rule_name = rule_name.toLowerCase();
			var css_rules = sheet.cssRules || sheet.rules,
				j = 0;
			do {
				if(css_rules.length && j > css_rules.length + 5) { return false; }
				if(css_rules[j].selectorText && css_rules[j].selectorText.toLowerCase() == rule_name) {
					if(delete_flag === true) {
						if(sheet.removeRule) { sheet.removeRule(j); }
						if(sheet.deleteRule) { sheet.deleteRule(j); }
						return true;
					}
					else { return css_rules[j]; }
				}
			}
			while (css_rules[++j]);
			return false;
		},
		add_css : function(rule_name, sheet) {
			if($.jstree.css.get_css(rule_name, false, sheet)) { return false; }
			if(sheet.insertRule) { sheet.insertRule(rule_name + ' { }', 0); } else { sheet.addRule(rule_name, null, 0); }
			return $.jstree.css.get_css(rule_name);
		},
		remove_css : function(rule_name, sheet) { 
			return $.jstree.css.get_css(rule_name, true, sheet); 
		},
		add_sheet : function(opts) {
			var tmp;
			if(opts.str) {
				tmp = document.createElement("style");
				tmp.setAttribute('type',"text/css");
				if(tmp.styleSheet) {
					document.getElementsByTagName("head")[0].appendChild(tmp);
					tmp.styleSheet.cssText = opts.str;
				}
				else {
					tmp.appendChild(document.createTextNode(opts.str));
					document.getElementsByTagName("head")[0].appendChild(tmp);
				}
				return tmp.sheet || tmp.styleSheet;
			}
			if(opts.url) {
				if(document.createStyleSheet) {
					try { tmp = document.createStyleSheet(opts.url); } catch (e) { }
				}
				else {
					tmp			= document.createElement('link');
					tmp.rel		= 'stylesheet';
					tmp.type	= 'text/css';
					tmp.media	= "all";
					tmp.href	= opts.url;
					document.getElementsByTagName("head")[0].appendChild(tmp);
					return tmp.styleSheet;
				}
			}
		}
	};
	// load the css when DOM is ready
	$(function() {
		// code is copied form jQuery ($.browser is deprecated + there is a bug in IE)
		var u = navigator.userAgent.toLowerCase(),
			v = (u.match( /.+?(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
			css_string = '' + 
				'.jstree ul, .jstree li { display:block; margin:0 0 0 0; padding:0 0 0 0; list-style-type:none; } ' + 
				'.jstree li { display:block; min-height:18px; line-height:18px; white-space:nowrap; margin-left:18px; } ' + 
				'.jstree > ul > li { margin-left:0px; } ' + 
				'.jstree ins { display:inline-block; text-decoration:none; width:18px; height:18px; margin:0 0 0 0; padding:0; } ' + 
				'.jstree a { display:inline-block; line-height:16px; height:16px; color:black; white-space:nowrap; text-decoration:none; padding:1px 2px; margin:0; } ' + 
				'.jstree a:focus { outline: none; } ' + 
				'.jstree a > ins { height:16px; width:16px; } ' + 
				'.jstree a > .icon { margin-right:3px; } ' + 
				'li.jstree-open > ul { display:block; } ' + 
				'li.jstree-closed > ul { display:none; } ';
		// Correct IE 6 (does not support the > CSS selector
		if(/msie/.test(u) && parseInt(v, 10) == 6) { 
			css_string += '' + 
				'.jstree li { height:18px; margin-left:0; } ' + 
				'.jstree li li { margin-left:18px; } ' + 
				'li.jstree-open ul { display:block; } ' + 
				'li.jstree-closed ul { display:none !important; } ' + 
				'.jstree li a { display:inline; } ' + 
				'.jstree li a ins { height:16px; width:16px; margin-right:3px; } ';
		}
		$.jstree._css.add_sheet({ str : css_string });
	});
})(jQuery);
//*/

/* 
 * jsTree HTML data 1.0
 * The HTML data store. Datastores are build by replacing the `load_node` and `is_loaded` functions.
 */
(function ($) {
	$.jstree.plugin("html_data", {
		__init : function () { 
			this.data.html_data.original_container_html = this.get_container().html().replace(/<\/([^>]+)>\s+</ig,"</$1><").replace(/>\s+<([a-z]{1})/ig,"><$1");
		},
		defaults : { 
			data : false,
			ajax : false,
			correct_state : false
		},
		_fn : {
			load_node : function (obj, s_call, e_call) { this.load_node_html(obj, s_call, e_call); },
			is_loaded : function (obj) { 
				obj = this._get_node(obj); 
				return obj == -1 || !obj || !this.get_settings().html_data.ajax || obj.is(".jstree-open, .jstree-leaf") || obj.children("ul").children("li").size() > 0;
			},
			load_node_html : function (obj, s_call, e_call) {
				var d,
					s = this.get_settings().html_data,
					error_func = function () {},
					success_func = function () {};
				switch(true) {
					case (!s.data && !s.ajax):
						if(!obj || obj == -1) {
							this.get_container()
								.html(this.data.html_data.original_container_html)
								.find("li, a").filter(function () { return this.firstChild.tagName !== "INS"; }).prepend("<ins class='icon'>&nbsp;</ins>");
							this.clean_node();
						}
						if(s_call) { s_call.call(); }
						break;
					case (!!s.data && !s.ajax) || (!!s.data && !!s.ajax && (!obj || obj === -1)):
						if(!obj || obj == -1) {
							d = $(s.data);
							if(!d.is("ul")) { d = $("<ul>").append(d); }
							this.get_container()
								.append(d)
								.find("li, a").filter(function () { return this.firstChild.tagName !== "INS"; }).prepend("<ins class='icon'>&nbsp;</ins>");
							this.clean_node();
						}
						if(s_call) { s_call.call(); }
						break;
					case (!s.data && !!s.ajax) || (!!s.data && !!s.ajax && obj && obj !== -1):
						obj = this._get_node(obj);
						error_func = function (x, t, e) {
							var ef = this.get_settings().html_data.ajax.error; 
							if(ef) { ef.call(this, x, t, e); }
							if(obj != -1 && obj.length) {
								obj.children(".jstree-loading").removeClass("jstree-loading");
								if(s.correct_state) { obj.removeClass("jstree-open jstree-closed").addClass("jstree-leaf"); }
							}
							if(e_call) { e_call.call(); }
						};
						success_func = function (d, t, x) {
							var sf = this.get_settings().html_data.ajax.success; 
							if(sf) { d = sf.call(this,d,t,x) || d; }
							d = $(d);
							if(!d.is("ul")) { d = $("<ul>").append(d); }
							if(obj == -1) { this.get_container().html(d).find("li, a").filter(function () { return this.firstChild.tagName !== "INS"; }).prepend("<ins class='icon'>&nbsp;</ins>"); }
							else { obj.children(".jstree-loading").removeClass("jstree-loading").append(d).find("li, a").filter(function () { return this.firstChild.tagName !== "INS"; }).prepend("<ins class='icon'>&nbsp;</ins>"); }
							this.clean_node(obj);
							if(s_call) { s_call.call(); }
						};
						s.ajax.context = this;
						s.ajax.error = error_func;
						s.ajax.success = success_func;
						if($.isFunction(s.ajax.data)) { s.ajax.data = s.ajax.data.call(null, obj); }
						$.ajax(s.ajax);
						break;
				}
			}
		}
	});
	$.jstree.defaults.plugins.push("html_data");
})(jQuery);
//*/

/* 
 * jsTree themes plugin 1.0
 * Adds support for themes
 *  Themes are included by the set_theme function.
 *  - the `theme_name` argument should be the class name without the "jstree-" part. 
 *  - the `theme_url` argument should be a path to a CSS file, it defaults to `$.jstree._themes` + `theme_name` + '/style.css'
 *  `$.jstree._themes` defaults to jquery.jstree.js path + '/themes'
 *  you can set `$.jstree._themes` after including this plugin
 */
(function ($) {
	var themes_loaded = [];
	$.jstree._themes = false;
	$.jstree.plugin("themes", {
		__init : function () { 
			this.get_container()
				.bind("jstree.init", $.proxy(function () {
					this.data.themes.dots = this.get_settings().themes.dots; 
					this.data.themes.icons = this.get_settings().themes.icons; 
					this.set_theme(this.get_settings().themes.theme);
				}, this));
		},
		defaults : { 
			theme : "default", 
			dots : true,
			icons : true
		},
		_fn : {
			set_theme : function (theme_name, theme_url) {
				if(!theme_name) { return false; }
				if(!theme_url) { theme_url = $.jstree._themes + theme_name + '/style.css'; }
				if($.inArray(theme_url, themes_loaded) == -1) {
					$.jstree._css.add_sheet({ "url" : theme_url, "rel" : "jstree" });
					themes_loaded.push(theme_url);
				}
				if(this.data.theme != theme_name) {
					this.get_container().removeClass('jstree-' + this.data.theme);
					this.data.themes.theme = theme_name;
				}
				this.get_container().addClass('jstree-' + theme_name);

				if(!this.data.themes.dots) { this.hide_dots(); }
				else { this.show_dots(); }
				if(!this.data.themes.icons) { this.hide_icons(); }
				else { this.show_icons(); }
			},
			get_theme	: function () { return this.data.themes.theme; },

			show_dots	: function () { this.data.themes.dots = true; this.get_container().children("ul").removeClass("jstree-no-dots"); },
			hide_dots	: function () { this.data.themes.dots = false; this.get_container().children("ul").addClass("jstree-no-dots"); },
			toggle_dots	: function () { if(this.data.themes.dots) { this.hide_dots(); } else { this.show_dots(); } },

			show_icons	: function () { this.data.themes.icons = true; this.get_container().children("ul").removeClass("jstree-no-icons"); },
			hide_icons	: function () { this.data.themes.icons = false; this.get_container().children("ul").addClass("jstree-no-icons"); },
			toggle_icons: function () { if(this.data.themes.icons) { this.hide_icons(); } else { this.show_icons(); } }
		}
	});
	$(function () {
		if($.jstree._themes === false) {
			$("script").each(function () { 
				if(this.src.toString().match(/jquery\.jstree[^\/]*?\.js(\?.*)?$/)) { 
					$.jstree._themes = this.src.toString().replace(/jquery\.jstree[^\/]*?\.js(\?.*)?$/, "") + 'themes/'; 
					return false; 
				}
			});
		}
		if($.jstree._themes === false) { $.jstree._themes = "themes/"; }
	});
	// Activate the plugin by default
	$.jstree.defaults.plugins.push("themes");
})(jQuery);
//*/

/* 
 * jsTree languages plugin 1.0
 * Adds support for multiple language versions in one tree
 * This basically allows for many titles coexisting in one node, but only one of them being visible at any given time
 * This is useful for maintaining the same structure in many languages (hence the name of the plugin)
 */
(function ($) {
	$.jstree.plugin("languages", {
		__init : function () { this._load_css();  },
		defaults : [],
		_fn : {
			set_lang : function (i) { 
				var langs = this.get_settings().languages,
					st = false,
					selector = ".jstree-" + this.get_index() + ' ul li a';
				if(!$.isArray(langs) || langs.length === 0) { return false; }
				if($.inArray(i,langs) == -1) {
					if(!!langs[i]) { i = langs[i]; }
					else { return false; }
				}
				if(i == this.data.languages.current_language) { return true; }
				st = $.jstree._css.get_css(selector + "." + this.data.languages.current_language, false, this.data.languages.language_css);
				if(st !== false) { st.style.display = "none"; }
				st = $.jstree._css.get_css(selector + "." + i, false, this.data.languages.language_css);
				if(st !== false) { st.style.display = ""; }
				this.data.languages.current_language = i;
				return true;
			},
			get_lang : function () {
				return this.data.languages.current_language;
			},
			get_text : function (obj, lang) {
				obj = this._get_node(obj);
				if(!obj.size()) { return false; }
				var langs = this.get_settings().languages;
				if($.isArray(langs) && langs.length) {
					lang = (lang && $.inArray(lang,langs) != -1) ? lang : this.data.languages.current_language;
					obj = obj.children("a." + lang);
				}
				else { obj = obj.children("a:eq(0)"); }
				obj = obj.contents().filter(function() { return this.nodeType == 3; })[0];
				return obj.nodeValue;
			},
			set_text : function (obj, val, lang) {
				obj = this._get_node(obj);
				if(!obj.size()) { return false; }
				var langs = this.get_settings().languages;
				if($.isArray(langs) && langs.length) {
					lang = (lang && $.inArray(lang,langs) != -1) ? lang : this.data.languages.current_language;
					obj = obj.children("a." + lang);
				}
				else { obj = obj.children("a:eq(0)"); }
				obj = obj.contents().filter(function() { return this.nodeType == 3; })[0];
				return (obj.nodeValue = val);
			},
			_load_css : function () {
				var langs = this.get_settings().languages,
					str = "/* languages css */",
					selector = ".jstree-" + this.get_index() + ' a',
					ln;
				if($.isArray(langs) && langs.length) {
					this.data.languages.current_language = langs[0];
					for(ln = 0; ln < langs.length; ln++) {
						str += selector + "." + langs[ln] + " {";
						if(langs[ln] != this.data.languages.current_language) { str += " display:none; "; }
						str += " } ";
					}
					this.data.language_css = $.jstree._css.add_sheet({ 'str' : str });
				}
			}
		}
	});
	$.jstree.defaults.plugins.push("languages");
})(jQuery);
//*/

/* 
 * jsTree types plugin 1.0
 * Adds support types of nodes
 * You can set an attribute on each li node, that represents its type.
 * According to the type setting the node may get custom icon/validation rules
 */
// TODO: replace all functions listed in `attach_to`
// TODO: inside the replaced functions check for this.data.types.enabled, check the rules, then call the original function
(function ($) {
	$.jstree.plugin("types", {
		__init : function () {
			this.get_container().bind("jstree.init", $.proxy(function (event) { 
				var types = this.get_settings().types.types, 
					attr  = this.get_settings().types.type_attr, 
					icons_css = "", 
					_this = this, 
					attach_to = [];

				$.each(types, function (i, tp) {
					$.each(tp, function (k, v) { 
						if(!/^(max_depth|max_children|icon|valid_children)$/.test(k)) { attach_to.push(k); }
					});
					if(!tp.icon) { return true; }
					if( tp.icon.image || tp.icon.position) {
						if(i == "default")	{ icons_css += '.jstree-' + _this.get_index() + ' a > .icon { '; }
						else				{ icons_css += '.jstree-' + _this.get_index() + ' li[' + attr + '=' + i + '] > a > .icon { '; }
						if(tp.icon.image)	{ icons_css += ' background-image:url(' + tp.icon.image + '); '; }
						if(tp.icon.position){ icons_css += ' background-position:' + tp.icon.position + '; '; }
						else				{ icons_css += ' background-position:0 0; '; }
						icons_css += '} ';
					}
				});
				if(icons_css != "") { $.jstree._css.add_sheet({ 'str' : icons_css }); }

				// attach to functions here

				this.enable_rules();
			}, this));
			if(this.data.move) {
				var s = this.get_settings(), of = s.move.check_move;
				s.move.check_move = function (m, is_copy) { 
					if(this.data.types.enabled && this._check_type_move(m, is_copy) === false) { return false; } 
					return of.call(this, m, is_copy);
				};
				this._set_settings(s);
			}
		},
		defaults : {
			// defines maximum number of root nodes (-1 means unlimited, -2 means disable max_children checking)
			max_children		: -1,
			// defines the maximum depth of the tree (-1 means unlimited, -2 means disable max_depth checking)
			max_depth			: -1,
			// defines valid node types for the root nodes
			valid_children		: "all",

			// where is the type stores (the rel attribute of the LI element)
			type_attr : "rel",
			// a list of types
			types : {
				// the default type
				"auto" : {
					"max_children"	: -1,
					"max_depth"		: -1,
					"valid_children": "all",

					// Bound functions - you can bind any other function here (using boolean or function)
					"select_node"	: false,
					"open_node"		: true,
					"close_node"	: true,

					"create_node"	: true,
					"delete_node"	: true,
					"cut"			: false,
					"copy"			: false,
					"paste"			: true
				},
				// the default type
				"manual" : {
					"max_children"	: 2,
					"max_depth"		: 2,
					"valid_children": "all",

					// Bound functions - you can bind any other function here (using boolean or function)
					"select_node"	: false,
					"open_node"		: true,
					"close_node"	: true,

					"create_node"	: true,
					"delete_node"	: true,
					"cut"			: false,
					"copy"			: false,
					"paste"			: true
				}
			}
		},
		_fn : {
			enable_rules : function () { this.data.types.enabled = true; },
			disable_rules : function () { this.data.types.enabled = false; },

			get_type : function (obj) {
				// TODO: maybe cache using data?
				obj = this._get_node(obj);
				return (!obj || !obj.length) ? false : obj.attr(this.get_settings().types.type_attr) || "default";
			},
			set_type : function (str, obj) {
				obj = this._get_node(obj);
				return (!obj.length || !str) ? false : obj.attr(this.get_settings().types.type_attr, str);
			},
			_check : function (rule, obj, opts) {
				// TODO : deal with obj - it may not be the node (create for example)
				var v = false, t = this.get_type(obj), d = 0, _this = this, s = this.get_settings().types;
				
				if(obj === -1) { 
					if(!!s[rule]) { v = s[rule]; }
					else { return; }
				}
				else {
					if(t === false) { return; }
					if(!!s.types[t] && !!s.types[t][rule]) { v = s.types[t][rule]; }
					else if(!!s.types["default"] && !!s.types["default"][rule]) { v = s.types["default"][rule]; }
				}
				if($.isFunction(v)) { v = v.call(null, obj, this); }
				if(rule === "max_depth" && obj !== -1 && opts !== false && s.max_depth !== -2 && v !== 0) {
					this._get_node(obj).parentsUntil(this.get_container(),"li").each(function (i) {
						d = _this._check(rule, this, false);
						if(d !== -1 && d - (i + 1) <= 0) { v = 0; return false; }
					});
				}
				return v;
			},
			_check_type_move : function(m, is_copy) {
				if(!m.r_t.data.types) { return true; }
				var s  = m.r_t.get_settings().types,
					mc = m.r_t._check("max_children", m.cr),
					md = m.r_t._check("max_depth", m.cr),
					vc = m.r_t._check("valid_chidren", m.cr),
					ch = 0, d = 1, t;
				if(vc === "none") { return false; } 
				if($.isArray(vc) && m.o_t.get_type) {
					m.o.each(function () {
						if(!$.inArray(m.o_t.get_type(this), vc)) { d = false; return false; }
					});
					if(d === false) { return false; }
				}
				if(s.max_children !== -2 && mc !== -1) {
					ch = m.cr === -1 ? this.get_container().children("> ul > li").not(m.o).length : m.cr.children("> ul > li").not(m.o).length;
					if(ch + m.o.length > mc) { return false; }
				}
				if(s.max_depth !== -2 && md !== -1) {
					d = 0;
					if(md === 0) { return false; }
					if(typeof m.o.d === "undefined") {
						// TODO: deal with progressive rendering and async when checking max_depth (how to know the depth of the moved node)
						t = m.o;
						while(t.length > 0) {
							t = t.find("> ul > li");
							d ++;
						}
						m.o.d = d;
					}
					if(md - m.o.d < 0) { return false; }
				}
			}
		}
	});
	$.jstree.defaults.plugins.push("types");
})(jQuery);
//*/

/* 
 * jsTree ui plugin 1.0
 * Adds all the neccesary events and functions for user interaction with the tree
 */
// TODO: create function, rename function (uses set_text at end) 
// TODO: selected_parent_delete - watch for event and handle - like close_node
(function ($) {
	$.jstree.plugin("ui", {
		__init : function () { 
			this.data.ui.selected = $(); 
			this.data.ui.hovered = false;

			this.get_container().bind("mousedown", $.proxy(function () { this.set_focus(); }, this)); // This used to be setTimeout(set_focus,0) - why?
			$("li > ins", this.get_container()[0]).live("click", $.proxy(function (event) {
				var trgt = $(event.target);
				if(trgt.is("ins") && event.pageY - trgt.offset().top < this.data.core.li_height) { this.toggle_node(trgt); }
			}, this));
			$("a", this.get_container()[0])
				.live("click", $.proxy(function (event) {
						var s = this.get_settings().ui,
							obj = this._get_node(event.currentTarget),
							is_multiple = (s.select_multiple_modifier == "on" || (s.select_multiple_modifier !== false && event[s.select_multiple_modifier + "Key"])),
							is_selected = this.is_selected(obj);
						switch(true) {
							case (is_selected && !is_multiple): break;
							case (!is_selected && !is_multiple): 
								if(s.select_limit == -1 || s.select_limit > 0) {
									this.deselect_all();
									this.select_node(obj);
								}
								break;
							case (is_selected && is_multiple): 
								this.deselect_node(obj);
								break;
							case (!is_selected && is_multiple): 
								if(s.select_limit == -1 || this.data.ui.selected.length + 1 <= s.select_limit) { this.select_node(obj); }
								break;
						}
						event.preventDefault();
					}, this))
				.live("mouseenter", $.proxy(function (event) {
						this.hover_node(event.target);
					}, this))
				.live("mouseleave", $.proxy(function (event) {
						this.dehover_node(event.target);
					}, this));
				// The above two cause problems when moving from/to a scrollbar (bug filed - 5682)

			this.get_container().bind("jstree.loaded", $.proxy(function () { 
				var s = this.get_settings().ui;
				this.data.ui.to_open = $.map($.makeArray(s.initially_open), function (n) { return "#" + n.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/'); });
				this.data.ui.to_select = $.map($.makeArray(s.initially_select), function (n) { return "#" + n.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/'); });
				this._restore();
			}, this));

			this.get_container().bind("jstree.close_node", $.proxy(function (event, data) { 
				var s = this.get_settings().ui,
					obj = this._get_node(data.arg[0]),
					clk = (obj && obj.length) ? obj.find(".jstree-clicked") : [],
					_this = this;
				if(s.selected_parent_close === false || !clk.length) { return; }
				clk.each(function () { 
					_this.deselect_node(this);
					if(s.selected_parent_close === "select_parent") { _this.select_node(obj); }
				});
			}, this));
		},
		defaults : {
			select_limit : -1, // 0, 1, 2 ... or -1 for unlimited
			select_multiple_modifier : "ctrl", // always on, or ctrl, shift, alt
			selected_parent_close : "select_parent", // false, "deselect", "select_parent"
			initially_select : [],
			initially_open : []
		},
		_fn : { 
			hover_node : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return false; }
				//if(this.data.ui.hovered && obj.get(0) === this.data.ui.hovered.get(0)) { return; }
				if(!obj.hasClass("jstree-hovered")) { this.dehover_node(); }
				this.data.ui.hovered = obj.children("a").addClass("jstree-hovered").parent();
			},
			dehover_node : function (obj) {
				if(obj) { obj = this._get_node(obj); }
				else { obj = this.data.ui.hovered; }
				if(!obj || !obj.length) { return false; }
				obj.children("a").removeClass("jstree-hovered");
			},

			select_node : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return false; }
				if(!this.is_selected(obj)) {
					obj.children("a").addClass("jstree-clicked");
					this.data.ui.selected = this.data.ui.selected.add(obj);
				}
			},
			deselect_node : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return false; }
				if(this.is_selected(obj)) {
					obj.children("a").removeClass("jstree-clicked");
					this.data.ui.selected = this.data.ui.selected.not(obj);
				}
			},
			toggle_select : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return false; }
				if(this.is_selected(obj)) { this.deselect_node(obj); }
				else { this.select_node(obj); }
			},
			is_selected : function (obj) { return this.data.ui.selected.index(this._get_node(obj)) >= 0; },
			get_selected : function (context) { 
				return context ? $(context).find(".jstree-clicked").parent() : this.data.ui.selected; 
			},
			deselect_all : function (context) {
				if(context) { $(context).find(".jstree-clicked").removeClass("jstree-clicked"); } 
				else { this.get_container().find(".jstree-clicked").removeClass("jstree-clicked"); }
				this.data.ui.selected = $([]);
			},
			refresh : function (obj) {
				var _this = this,
					opn = $([]),
					sel = $([]);
				obj = !obj || obj == 1 ? -1 : this._get_node(obj);
				this.data.ui.to_open = [];
				this.data.ui.to_select = [];
				opn = (obj == -1) ? this.get_container().find(".jstree-open") : obj.parent().find(".jstree-open");
				sel = (obj == -1) ? this.get_container().find(".jstree-clicked").parent() : obj.parent().find(".jstree-clicked");
				opn.each(function () { _this.data.ui.to_open.push("#" + this.id.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/')); });
				sel.each(function () { _this.data.ui.to_select.push("#" + this.id.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/')); });
				this.load_node(obj, function () { _this._restore(); });
			},
			delete_node : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return false; }
				if(this.is_selected(obj)) { this.deselect_node(obj); }
				var p = this._get_parent(obj);
				obj = obj.remove();
				this.clean_node(p);
				return obj;
			},

			_restore : function (is_callback) {
				var _this = this,
					reselect = true,
					current = [],
					remaining = [];
				if(this.data.ui.to_open.length) {
					$.each(this.data.ui.to_open, function (i, val) {
						if(val == "#") { return true; }
						if($(val).length && $(val).is(".jstree-closed")) { current.push(val); }
						else {remaining.push(val); }
					});
					if(current.length) {
						this.data.ui.to_open = remaining;
						$.each(current, function (i, val) { 
							_this.open_node(val, function () { _this._restore(true); }); 
						});
						reselect = false;
					}
				}
				if(reselect) {
					$.each(this.data.ui.to_select, function (i, val) {
						_this.select_node(val);
					});
				}
			}
		}
	});
	$.jstree.defaults.plugins.push("ui");
	$.jstree.register_rollback("select_node deselect_node");
})(jQuery);
//*/

/*
 * jsTree cookies plugin 1.0
 * Stores the currently opened/selected nodes in a cookie and then restores them
 * Depends on the ui plugin & the jquery.cookie plugin
 */
(function ($) {
	$.jstree.plugin("cookies", {
		__init : function () {
			if(typeof $.cookie === "undefined") { throw "jsTree cookie: jQuery cookie plugin not included."; }

			var s = this.get_settings().cookies;
			if(!!s.open) {
				this.get_container()
					.bind("jstree.open_node", $.proxy(function () { this._set_cookie("open"); }, this))
					.bind("jstree.close_node", $.proxy(function () { this._set_cookie("open"); }, this));
			}
			if(!!s.select) {
				this.get_container()
					.bind("jstree.select_node", $.proxy(function () { this._set_cookie("select"); }, this))
					.bind("jstree.deselect_node", $.proxy(function () { this._set_cookie("select"); }, this));
			}
		},
		defaults : {
			open : "jstree_open",
			select : "jstree_select",
			options : { }
		},
		_fn : {
			_restore : function (is_callback) {
				if(!is_callback) {
					var s = this.get_settings().cookies, 
						v = [];

					v = $.cookie(s.open);
					if(v) {
						v = v.split(",");
						if(v.length) { this.data.ui.to_open = v; }
					}
					v = $.cookie(s.select);
					if(v) {
						v = v.split(",");
						if(v.length) { this.data.ui.to_select = v; }
					}
				}
				arguments.callee.old.call(this);
			},
			_set_cookie : function (t) {
				var s = this.get_settings().cookies,
					v = [];
				if(!!!s[t]) { return; }
				switch(t) {
					case "open":
						this.get_container().find(".jstree-open").each(function () { if(this.id) { v.push("#" + this.id.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/')); } });
						break;
					case "select":
						this.get_container().find(".jstree-clicked").parent().each(function () { if(this.id) { v.push("#" + this.id.toString().replace(/^#/,"").replace('\\/','/').replace('/','\\/')); } });
						break;
				}
				$.cookie(s[t], v, s.options);
			}
		}
	});
	$.jstree.defaults.plugins.push("cookies");
})(jQuery);
//*/

/*
 * jsTree hotkeys plugin 1.0
 * Enables keyboard navigation for all tree instances
 * Depends on the jstree ui & jquery hotkeys plugins
 */
(function ($) {
	var bound = [];
	function exec(i) {
		var f = $.jstree._focused();
		if(f && f.data.hotkeys.enabled) { return f.get_settings().hotkeys[i].apply(f); }
	}
	$.jstree.plugin("hotkeys", {
		__init : function () {
			if(typeof window.hotkeys == "undefined") { throw "jsTree hotkeys: jQuery hotkeys plugin not included."; }
			$.each(this.get_settings().hotkeys, function (i, val) {
				if($.inArray(i, bound) == -1) {
					$(document).bind("keydown", { combi : i, disableInInput: true }, function (event) { return exec(i); });
					bound.push(i);
				}
			});
			this.enable_hotkeys();
		},
		defaults : {
			"up"	: function () { 
				this.hover_node(this._get_prev(this.data.ui.hovered || this.data.ui.selected[this.data.ui.selected.length - 1]));
				return false; 
			},
			"down"	: function () { 
				this.hover_node(this._get_next(this.data.ui.hovered || this.data.ui.selected[this.data.ui.selected.length - 1]));
				return false;
			},
			"left"	: function () { 
				var o = this.data.ui.hovered || this.data.ui.selected[this.data.ui.selected.length - 1];
				if(o) {
					if(o.hasClass("jstree-open")) { this.close_node(o); }
					else { this.hover_node(this._get_prev(o)); }
				}
				return false;
			},
			"right"	: function () { 
				var o = this.data.ui.hovered || this.data.ui.selected.eq(this.data.ui.selected.length - 1);
				if(o && o.length) {
					if(o.hasClass("jstree-closed")) { this.open_node(o); }
					else { this.hover_node(this._get_next(o)); }
				}
				return false;
			},
			"space"	: function () { 
				if(this.data.ui.hovered) { this.data.ui.hovered.children("a:eq(0)").click(); } 
				return false; 
			}
		},
		_fn : {
			enable_hotkeys : function () {
				this.data.hotkeys.enabled = true;
			},
			disable_hotkeys : function () {
				this.data.hotkeys.enabled = false;
			}
		}
	});
	$.jstree.defaults.plugins.push("hotkeys");
})(jQuery);
//*/

/*
 * jsTree search plugin 1.0
 * Enables both sync and async search on the tree
 */
// TODO: work with progressive render?
(function ($) {
	$.expr[':'].jstree_contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toLowerCase().indexOf(m[3].toLowerCase())>=0;
	};
	$.jstree.plugin("search", {
		__init : function () {
			this.data.search.str = "";
			this.data.search.result = $([]);
		},
		defaults : {
			ajax : false, // OR ajax object
			case_insensitive : false
		},
		_fn : {
			search : function (str, skip_async) {
				var s = this.get_settings().search, 
					error_func = function () { },
					success_func = function () { };
				this.data.search.str = str;

				if(!skip_async && s.ajax !== false && this.get_container().find(".jstree-closed:eq(0)").length > 0) {
					this.search.supress_callback = true;
					error_func = function () { };
					success_func = function (d) {
						this.data.search.to_open = d;
						this._search_open();
					};
					s.ajax.error = s.ajax.error ? function (x,t,e) { s.ajax.error.call(null,x,t,e); error_func.call(); } : error_func;
					s.ajax.success = s.ajax.success ? function (d,t,x) { d = s.ajax.success.call(null,d,t,x) || d; success_func.call(this, d); } : success_func;
					if($.isFunction(s.ajax.data)) { s.ajax.data = s.ajax.data.call(this); }
					if(!s.ajax.dataType || /^json/.exec(s.ajax.dataType)) { s.ajax.dataType = "json"; }
					$.ajax(s.ajax);
					return;
				}
				this.search.supress_callback = false;
				this.clear_search();
				this.data.search.result = this.get_container().find("a:" + (s.case_insensitive ? "jstree_contains" : "contains") + "(" + this.data.search.str + ")").filter(":visible");
				this.data.search.result.addClass("jstree-search");
			},
			clear_search : function (str) {
				this.data.search.result.removeClass("jstree-search");
			},
			_search_open : function (is_callback) {
				var _this = this,
					done = true,
					current = [],
					remaining = [];
				if(this.data.search.to_open.length) {
					$.each(this.data.search.to_open, function (i, val) {
						if(val == "#") { return true; }
						if($(val).length && $(val).is(".jstree-closed")) { current.push(val); }
						else { remaining.push(val); }
					});
					if(current.length) {
						this.data.search.to_open = remaining;
						$.each(current, function (i, val) { 
							_this.open_node(val, function () { _this._search_open(true); }); 
						});
						done = false;
					}
				}
				if(done) { this.search(this.data.search.str, true); }
			}
		}
	});
	$.jstree.defaults.plugins.push("search");
})(jQuery);
//*/

/*
 * jsTree checkbox plugin 1.0
 * Inserts checkboxes in front of every node
 * Depends on the ui plugin
 */
(function ($) {
	$.jstree.plugin("checkbox", {
		__init : function () {
			this.get_container()
				.bind("jstree.open_node", $.proxy(function (event, data) { 
						this._prepare_checkboxes(data.arg[0]);
					}, this))
				.bind("jstree.loaded", $.proxy(function (event) {
						this._prepare_checkboxes();
					}, this))
				.bind("jstree.clean_node", $.proxy(function (event, data) {
						this._repair_state(data.arg[0]);
					}, this));
			if(this.get_settings().checkbox.override_select) {
				this.hover_node = this.dehover_node = this.select_node = this.deselect_node = this.deselect_all = $.noop;
				this.get_selected = this.get_checked;
				$("a", this.get_container())
					.live("click", $.proxy(function (event) {
							this.change_state(event.target);
							event.preventDefault();
						}, this));
			}
			else {
				$(".checkbox", this.get_container())
					.live("click", $.proxy(function (event) {
							this.change_state(event.target);
							event.stopImmediatePropagation();
							return false;
						}, this));
			}
		},
		defaults : {
			override_select : true
		},
		_fn : {
			_prepare_checkboxes : function (obj) {
				obj = !obj || obj == -1 ? this.get_container() : this._get_node(obj);
				var c = obj.is("li") && obj.hasClass("jstree-checked") ? "jstree-checked" : "jstree-unchecked";
				obj.find("a").not(":has(.checkbox)").prepend("<ins class='checkbox'>&nbsp;</ins>").parent().addClass(c);
			},
			change_state : function (obj, state) {
				obj = this._get_node(obj);
				state = (state === false || state === true) ? state : obj.hasClass("jstree-checked");
				if(state) { obj.find("li").andSelf().removeClass("jstree-checked jstree-undetermined").addClass("jstree-unchecked"); }
				else { obj.find("li").andSelf().removeClass("jstree-unchecked jstree-undetermined").addClass("jstree-checked"); }

				var _this = this;
				obj.parentsUntil(this.get_container(), "li").each(function () {
					var $this = $(this);
					if(state) {
						if($this.children("ul").children(".jstree-checked").length) {
							$this.parentsUntil(_this.get_container(), "li").andSelf().removeClass("jstree-checked jstree-unchecked").addClass("jstree-undetermined");
							return false;
						}
						else {
							$this.removeClass("jstree-checked jstree-undetermined").addClass("jstree-unchecked");
						}
					}
					else {
						if($this.children("ul").children(".jstree-unchecked, .jstree-undetermined").length) {
							$this.parentsUntil(_this.get_container(), "li").andSelf().removeClass("jstree-checked jstree-unchecked").addClass("jstree-undetermined");
							return false;
						}
						else {
							$this.removeClass("jstree-unchecked jstree-undetermined").addClass("jstree-checked");
						}
					}
				});
			},
			check_node : function (obj) {
				this.change_state(obj, false);
			},
			uncheck_node : function (obj) {
				this.change_state(obj, true);
			},
			check_all : function () {
				var _this = this;
				this.get_container().children("ul").children("li").each(function () {
					_this.check_node(this, false);
				});
			},
			uncheck_all : function () {
				var _this = this;
				this.get_container().children("ul").children("li").each(function () {
					_this.check_node(this, true);
				});
			},

			is_checked : function(obj) {
				obj = this._get_node(obj);
				return obj.length ? obj.is(".jstree-checked") : false;
			},
			get_checked : function (obj) {
				obj = !obj || obj === -1 ? this.get_container() : this._get_node(obj);
				return obj.find("> ul > .jstree-checked, .jstree-undetermined > ul > .jstree-checked");
			},
			get_unchecked : function (obj) { 
				obj = !obj || obj === -1 ? this.get_container() : this._get_node(obj);
				return obj.find("> ul > .jstree-unchecked, .jstree-undetermined > ul > .jstree-unchecked");
			},

			show_checkboxes : function () { this.get_container().children("ul").removeClass("jstree-no-checkboxes"); },
			hide_checkboxes : function () { this.get_container().children("ul").addClass("jstree-no-checkboxes"); },

			_repair_state : function (obj) {
				obj = this._get_node(obj);
				if(!obj.length) { return; }
				var a = obj.find("> ul > .jstree-checked").length,
					b = obj.find("> ul > li").length;
				if(b === 0) {
					if(obj.hasClass("jstree-undetermined")) { this.check_node(obj); }
				}
				else if(a === 0) { this.uncheck_node(obj); }
				else if(a === b) { this.check_node(obj); }
				else { 
					obj.parentsUntil(this.get_container(),"li").andSelf().removeClass("jstree-checked jstree-unchecked").addClass("jstree-undetermined");
				}
			}
		}
	});
	$.jstree.defaults.plugins.push("checkbox");
})(jQuery);
//*/

/*
 * jsTree move plugin 1.0
 * Adds support for moving, copying and pasting nodes in a tree (or across trees)
 */
// TODO: lang cleanup
// TODO: default drop-out and drop-in
// TODO: drag handles? + SCROLLING WHEN NEAR EDGE (look at jQuery UI)
// TODO: maybe set_drag function, so that checkbox and select may use it (so that all selected or all checked nodes are dragged).
(function ($) {
	$.jstree._move = { 
		data : {},
		is_down : false,
		is_drag : false,
		prepare_drag : function (obj, t, txt, e) {
			if(!txt) { txt = t ? t.get_text(obj) : "Dragged node"; }

			$.jstree._move.is_down = true;
			$.jstree._move.data = { 
				o		: obj, 
				o_t		: t ? t : false, 
				hlp		: $("<div id='jstree-dragged'><ins>&nbsp;</ins>" + txt + "</div>").css("opacity", "0.75"), 
				init_x	: e ? e.pageX : false, 
				init_y	: e ? e.pageY : false 
			};
			$(document).bind("mousemove", $.jstree._move.drag);
			$(document).bind("mouseup", $.jstree._move.stop_drag);
		},
		start_drag : function () {
			$.jstree._move.is_drag = true;
			$.jstree._move.data.hlp.appendTo("body");
			$(document).triggerHandler("jstree.start_drag", { move : $.jstree._move.data });
		},
		drag : function (e) {
			if(!$.jstree._move.is_down) { return; }
			if(!$.jstree._move.is_drag) {
				if(Math.abs(e.pageX - $.jstree._move.data.init_x) > 5 || Math.abs(e.pageY - $.jstree._move.data.init_y) > 5) { $.jstree._move.start_drag(); }
				else { return; }
			}

			try { 
				if($.jstree._move.data.state === 1 && e.target.id !== "jstree-marker" && $.jstree._move.data.r && !$.contains($.jstree._move.data.r.get(0), e.target)) { $.jstree._move.data.state = 0; }
			} catch(err) { }
			$(document).triggerHandler("jstree.drag", { 'move' : $.jstree._move.data, 'event' : e });

			$.jstree._move.data.hlp.css({ left : (e.pageX + 5) + "px", top : (e.pageY + 10) + "px" }).attr("class", function () {
				if($.jstree._move.data.state === 1) { return "jstree-ok"; }
				if($.jstree._move.data.state === 0) { return "jstree-invalid"; }
			});
		},
		stop_drag : function (skip_move) {
			$.jstree._move.is_down = false;

			if($.jstree._move.is_drag) {
				$.jstree._move.is_drag = false;
				$.jstree._move.data.hlp.remove();
				$(document).unbind("mousemove", $.jstree._move.drag);
				$(document).unbind("mouseup", $.jstree._move.stop_drag);
				$(document).triggerHandler("jstree.stop_drag", { move : $.jstree._move.data });
				if(skip_move !== true && $.jstree._move.data.o_t !== -1 && $.jstree._move.data.r_t !== -1 && $.jstree._move.data.state === 1) { $.jstree._move.data.r_t.move($.jstree._move.data, false, false, false, true, true); }
			}
			$.jstree._move.data = { };
		}
	};
	$.jstree.plugin("move", {
		__init : function () { 
			var s = this.get_settings().move;
			this.data.move = {}; 
			if(s.drag_n_drop) {
				$("a", this.get_container()[0])
					.live("mousedown", $.proxy(function (e) { 
						try {
							e.currentTarget.unselectable = "on";
							e.currentTarget.onselectstart = function() { return false; };
							if(e.currentTarget.style) { e.currentTarget.style.MozUserSelect = "none"; }
						} catch(err) { }
						var o = this._get_node(e.currentTarget);
						$.jstree._move.prepare_drag(o, this, this.get_text(o), e);
						return false;
					}, this))
					.live("mousemove", $.proxy(function (e) { 
						if(!$.jstree._move.is_drag) { return; }
						this._check_move(this._prepare_move(false, e.currentTarget, "inside"));
						// TODO: calculate position & stuff
					}, this));
			}
		},
		defaults : {
			move_from : [],
			move_to : [],
			// TODO: implement always_copy
			always_copy : false, // false, true or "multitree"
			copy_modifier : "ctrl",
			drag_n_drop : true,
			default_position : "bottom",
			check_move : function (m) { }
		},
		_fn : {
			_check_move : function(m) { 
				var r, os, rs;
				if(m === false) { return false; }
				if(m === -1) { $.jstree._move.data.state = -1; return -1; }
				// TODO: replace with jQuery contains?
				if(m.r.get(0) === m.o.get(0) || (m.r_t && m.r_t !== 1 && m.o.index(m.r.parentsUntil(m.r_t.get_container(), "li").andSelf())) !== -1) { $.jstree._move.data.state = 0; return false; }
				
				if(m.o_t !== m.r_t && m.o_t !== -1) {
					os = m.o_t.get_settings().move;
					rs = m.r_t.get_settings().move;
					if(os.move_to === "none" || os.move_to.length === 0 || rs.move_from === "none" || rs.move_from.length === 0) {
						$.jstree._move.data.state = 0; 
						return false;
					}
					if(
						(os.move_to !== "all" && !m.r_t.get_container().is(os.move_to.join(","))) || 
						(rs.move_from !== "all" && !m.o_t.get_container().is(rs.move_from.join(",")))) {
						$.jstree._move.data.state = 0; 
						return false;
					}
				}
				r = this.get_settings().move.check_move.apply(this, arguments);
				if(r === true) { $.jstree._move.data.state = 1; return true; }
				if(r === false) { $.jstree._move.data.state = 0; return false; }
				$.jstree._move.data.state = 1; 
				return true;
			},
			_prepare_move : function (obj, ref, position, callback) { 
				var m = $.jstree._move.data, 
					s = this.get_settings().move,
					_this = this;

				if(!m.o_t) { m.o_t = $.jstree._reference(obj); }
				if(!m.o_t) { return false; }
				if(obj) { m.o = m.o_t._get_node(obj); }
				m.r = ref == -1 ? -1 : this._get_node(ref);
				if(m.r != -1 && (!m.r || !m.r.length)) { return false; }
				m.r_t = this;
				m.p = position || s.default_position;

				// begin calculation
				if(m.r == -1) {
					m.cr = -1;
					switch(m.p) {
						case "top":
						case "before":
							m.cp = 0; 
							break;
						case "after":
						case "bottom":
							m.cp = m.r_t.get_container().find(" > ul > li").length; 
							break;
						default:
							m.cp = m.p;
							break;
					}
				}
				else {
					if(!/^(before|after)$/.test(m.p) && !this.is_loaded(m.r)) {
						this.load_node(m.r, function () { _this.prepare_move(obj, ref, position, callback); });
						return false;
					}
					switch(m.p) {
						case "before":
							m.cp = m.r.index();
							m.cr = m.r_t._get_parent(m.r);
							break;
						case "after":
							m.cp = m.r.index();
							m.cr = m.r_t._get_parent(m.r);
							break;
						case "inside":
						case "top":
							m.cp = 0;
							m.cr = m.r;
							break;
						case "bottom":
							m.cp = m.r.find(" > ul > li").length; 
							m.cr = m.r;
							break;
						default: 
							m.cp = m.p;
							m.cr = m.r;
							break;
					}
				}
				if(callback) { callback.call(this, m); }
				return m;
			},
			move : function (obj, ref, position, is_copy, skip_check, _is_prepared) { 
				var _this = this,
					m, np, op, ul, r, o;
				this.move.supress_callback = true;
				if(!_is_prepared) { 
					this._prepare_move(obj, ref, position, function (d) { _this.move(d, false, false, is_copy, skip_check, true); });
					return;
				}

				m = obj;
				if(!skip_check && this._check_move(m, is_copy) === false) { return; }
				this.move.supress_callback = false;

				np = m.cr == -1 ? m.r_t.get_container() : m.cr;
				ul = np.children("ul:eq(0)");
				if(!ul.length) { ul = $("<ul>").appendTo(np); }
				op = m.o_t._get_parent(m.o);
				r = ul.children("li:nth-child(" + (m.cp + 1) + ")");
				o = false;

				if(is_copy) {
					o = m.o.clone();
					o.find("*[id]").andSelf().each(function () {
						if(this.id) { this.id = "copy_" + this.id; }
					});
				}
				else { o = m.o; }

				if(r.length) { r.before(o); }
				else { ul.append(o); }

				// TODO: sync type attributes (may be different if different trees)

				try { 
					m.o_t.clean_node(op);
					m.r_t.clean_node(np);
				} catch (e) { }
			},

			cut : function (obj) { $.jstree._move.data.o = obj; $.jstree._move.data.t = "cut";  },
			copy : function (obj) { this.cut(obj); $.jstree._move.data.t = "copy"; },
			paste : function (obj, position) { this.move($.jstree._move.data.o, obj, position, ($.jstree._move.data.t === "copy") ); }
		}
	});
	$(function() {
		var css_string = '' + 
			'#jstree-dragged { display:block; margin:0 0 0 0; padding:4px 4px 4px 24px; position:absolute; left:-2000px; top:-2000px; line-height:18px; border:1px solid red; } ' + 
			'#jstree-dragged ins { background:gray; display:block; text-decoration:none; width:18px; height:18px; margin:0 0 0 0; padding:0; position:absolute; top:4px; left:4px; } ' + 
			'#jstree-dragged.jstree-ok ins { background:green; } ' + 
			'#jstree-dragged.jstree-invalid ins { background:red; } ';
		$.jstree._css.add_sheet({ str : css_string });
	});
	$.jstree.defaults.plugins.push("move");
	$.jstree.register_rollback("move");
})(jQuery);
//*/