(function($)
{
  $.dbg = function()
  {
    if (typeof console !== 'object' || !$.dm.ctrl.options.debug) 
    {
      return;
    }
    try 
    {
      console.debug(arguments);
    } 
    catch(e) 
    {
			
			alert(e);
      for (var i in arguments) 
      {
        if (i < 5) 
          alert(arguments[i]);
      }
    }
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
    noRightClick: function()
    {
      this.each(function()
      {
        $(this)[0].oncontextmenu = function()
        {
          return false;
        }
      });
    },
    bindKey: function(data, fn)
    {
      return this.bind('keydown', data, function(e)
      {
        e.stopPropagation();
        return fn(e);
      });
    }
  });
  
})(jQuery);