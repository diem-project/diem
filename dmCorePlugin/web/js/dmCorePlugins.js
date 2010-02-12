(function($)
{
  $.dbg = function()
  {
    if (typeof console !== 'object' || ($.dm.ctrl && $.dm.ctrl.options && !$.dm.ctrl.options.debug)) 
    {
      return;
    }
    try 
    {
      console.debug(arguments);
    } 
    catch(e) 
    {
    }
  };
 
 $.loadStylesheets = function(stylesheets)
  {
    $.each(stylesheets, function()
    {
      if (!$('link[rel=stylesheet][href=' + this + ']').length) 
      {
        $("head").append('<link rel="stylesheet" href="' + this + '">');
      }
    });
  };
  
  $.fn.extend({
    orNot: function()
    {
      return this.length == 0 ? false : this;
    },
    rebind: function(type, data, fn)
    {
      return this.unbind(type, fn).bind(type, data, fn);
    },
    bindKey: function(key, fn)
    {
      if( !window.hotkeys)
      {
        return this;
      }
      
      return this.bind('keydown', key, function(e)
      {
        e.stopPropagation();
        return fn(e);
      });
    },
    unbindKey: function(key)
    {
      if( !window.hotkeys)
      {
        return this;
      }
      
      return this.unbind('keydown', key);
    }
  });
  
})(jQuery);