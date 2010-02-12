(function($)
{

  $.widget('ui.dmCodeArea', 
  {
    options: {
      lines: true,
      tab: true,
      save: null,
      esc: null
    },
    
    _init: function()
    {
      var self = this;
      
      this.element.css({
    'fontFamily': 'monospace',
     'fontSize': '12px',
     'lineHeight': '15px',
        'backgroundImage': 'url('+$.dm.ctrl.options.dm_core_asset_root+'images/codeArea/lines.png)',
        'backgroundRepeat': 'no-repeat',
        'backgroundPosition': '0 0',
     'paddingLeft': '28px'
    }).unbind().addClass("dm_code_area").focus();
      
      if (this.options.lines) 
      {
        this.element.addClass("dm_lines").bind("scroll", function()
        {
          self.element[0].style.backgroundPosition = "0 " + (-self.element[0].scrollTop) + "px";
        });
      }
      
      if (this.options.tab) 
      {
        this.element.tabby();
      }
      
      if ($.isFunction(this.options.save))
      {
        this.element.bindKey('Ctrl+s', this.options.save);
      }
      if ($.isFunction(this.options.esc)) 
      {
        this.element.bindKey('Esc', this.options.esc);
      }
    }
    
  });
  
  $.fn.tabby = function(options)
  {
    // build main options before element iteration
    var opts = $.extend({}, $.fn.tabby.defaults, options);
    var pressed = $.fn.tabby.pressed;
    
    // iterate and reformat each matched element
    return this.each(function()
    {
      $this = $(this);
      
      // build element specific options
      var options = $.meta ? $.extend({}, opts, $this.data()) : opts;
      
      $this.bind('keydown', function(e)
      {
        var kc = $.fn.tabby.catch_kc(e);
        if (16 == kc) 
          pressed.shft = true;
        /*
         because both CTRL+TAB and ALT+TAB default to an event (changing tab/window) that
         will prevent js from capturing the keyup event, we'll set a timer on releasing them.
         */
        if (17 == kc) 
        {
          pressed.ctrl = true;
          setTimeout("$.fn.tabby.pressed.ctrl = false;", 1000);
        }
        if (18 == kc) 
        {
          pressed.alt = true;
          setTimeout("$.fn.tabby.pressed.alt = false;", 1000);
        }
        
        if (9 == kc && !pressed.ctrl && !pressed.alt) 
        {
          e.preventDefault; // does not work in O9.63 ??
          pressed.last = kc;
          setTimeout("$.fn.tabby.pressed.last = null;", 0);
          process_keypress($(e.target).get(0), pressed.shft, options);
          return false;
        }
        
      }).bind('keyup', function(e)
      {
        if (16 == $.fn.tabby.catch_kc(e)) 
          pressed.shft = false;
      }).bind('blur', function(e)
      { // workaround for Opera -- http://www.webdeveloper.com/forum/showthread.php?p=806588
        if (9 == pressed.last) 
          $(e.target).one('focus', function(e)
          {
            pressed.last = null;
          }).get(0).focus();
      });
      
    });
  };
  
  // define and expose any extra methods
  $.fn.tabby.catch_kc = function(e)
  {
    return e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
  };
  $.fn.tabby.pressed = {
    shft: false,
    ctrl: false,
    alt: false,
    last: null
  };
  
  function process_keypress(o, shft, options)
  {
    var scrollTo = o.scrollTop;
    
    // gecko; o.setSelectionRange is only available when the text box has focus
    if (o.setSelectionRange) 
      gecko_tab(o, shft, options);
    
    o.scrollTop = scrollTo;
  }
  
  // plugin defaults
  $.fn.tabby.defaults = {
    tabString: "  "
  };
  
  function gecko_tab(o, shft, options)
  {
    var ss = o.selectionStart;
    var es = o.selectionEnd;
    
    // when there's no selection and we're just working with the caret, we'll add/remove the tabs at the caret, providing more control
    if (ss == es) 
    {
      // SHIFT+TAB
      if (shft) 
      {
        // check to the left of the caret first
        if ("\t" == o.value.substring(ss - options.tabString.length, ss)) 
        {
          o.value = o.value.substring(0, ss - options.tabString.length) + o.value.substring(ss); // put it back together omitting one character to the left
          o.focus();
          o.setSelectionRange(ss - options.tabString.length, ss - options.tabString.length);
        }
        // then check to the right of the caret
        else 
          if ("\t" == o.value.substring(ss, ss + options.tabString.length)) 
          {
            o.value = o.value.substring(0, ss) + o.value.substring(ss + options.tabString.length); // put it back together omitting one character to the right
            o.focus();
            o.setSelectionRange(ss, ss);
          }
      }
      // TAB
      else 
      {
        o.value = o.value.substring(0, ss) + options.tabString + o.value.substring(ss);
        o.focus();
        o.setSelectionRange(ss + options.tabString.length, ss + options.tabString.length);
      }
    }
    // selections will always add/remove tabs from the start of the line
    else 
    {
      // split the textarea up into lines and figure out which lines are included in the selection
      var lines = o.value.split("\n");
      var indices = new Array();
      var sl = 0; // start of the line
      var el = 0; // end of the line
      var sel = false;
      for (var i in lines) 
      {
        el = sl + lines[i].length;
        indices.push({
          start: sl,
          end: el,
          selected: (sl <= ss && el > ss) || (el >= es && sl < es) || (sl > ss && el < es)
        });
        sl = el + 1;// for "\n"
      }
      
      // walk through the array of lines (indices) and add tabs where appropriate           
      var modifier = 0;
      for (var i in indices) 
      {
        if (indices[i].selected) 
        {
          var pos = indices[i].start + modifier; // adjust for tabs already inserted/removed
          // SHIFT+TAB
          if (shft && options.tabString == o.value.substring(pos, pos + options.tabString.length)) 
          { // only SHIFT+TAB if there's a tab at the start of the line
            o.value = o.value.substring(0, pos) + o.value.substring(pos + options.tabString.length); // omit the tabstring to the right
            modifier -= options.tabString.length;
          }
          // TAB
          else 
            if (!shft) 
            {
              o.value = o.value.substring(0, pos) + options.tabString + o.value.substring(pos); // insert the tabstring
              modifier += options.tabString.length;
            }
        }
      }
      o.focus();
      var ns = ss + ((modifier > 0) ? options.tabString.length : (modifier < 0) ? -options.tabString.length : 0);
      var ne = es + modifier;
      o.setSelectionRange(ns, ne);
    }
  }
  
})(jQuery);