(function($)
{

  $.dbg = function()
  {
    if (!$.dm.ctrl.options.debug) 
    {
      return;
    }
    try 
    {
      console.debug(arguments);
    } 
    catch (e) 
    {
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
    hint: function()
    {
      var blurClass = 'blur';
      return this.each(function()
      {
        var $input = $(this);
        
        var message = $input.attr('title'), $form = $(this.form), $win = $(window);
        
        var remove = function()
        {
          if ($input.val() === message && $input.hasClass(blurClass)) 
          {
            $input.val('').removeClass(blurClass);
          }
        };
        
        // only apply logic if the element has the attribute
        if (message) 
        {
          // on blur, set value to title attr if text is blank
          $input.blur(function()
          {
            if (this.value === '') 
            {
              $input.val(message).addClass(blurClass);
            }
          }).focus(remove).blur(); // now change all inputs to title
          // clear the pre-defined text when form is submitted
          $form.submit(remove);
          $win.unload(remove); // handles Firefox's autocomplete
        }
      });
    }
  });
  
})(jQuery);