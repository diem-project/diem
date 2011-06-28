(function($)
{

  $.widget('ui.dmFrontPageBar', $.extend({}, $.dm.corePageBar, {
  
    _init: function()
    {
      this.initPageBar(this);
    },
    
    loaded: function()
    {
      this.element.find('a').each(function() {
        $(this).attr('href', $.dm.ctrl.options.script_name+$(this).attr('href'));
        
        // We need to remove the href because IE can't enable dragging with it
        if($.browser.msie)
        {
          $(this).data('a_href', $(this).attr('href'));
          $(this).removeAttr('href');
        }
      });
    },

    extendTreeOptions: function($tree, options)
    {
      options.selected = ['dmp' + $.dm.ctrl.options.page_id];

      options.callback.ondblclk = function(NODE, TREE_OBJ)
      {
        $('body').block();
        
        // Redirection under IE as we removed href
        if($.browser.msie)
        {
          location.href = $('a', NODE).data('a_href');
        }
        else
        {
          location.href = $('a', NODE).attr('href');
        }
        return false;
      }

      return options;
    }
    
  }));
  
})(jQuery);