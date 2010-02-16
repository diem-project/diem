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
      });
    },

    extendTreeOptions: function($tree, options)
    {
      options.selected = 'dmp' + $.dm.ctrl.options.page_id;

      options.callback.ondblclk = function(NODE, TREE_OBJ)
      {
        $('body').block();
        location.href = $('a', NODE).attr('href');
        return false;
      }

      return options;
    }
    
  }));
  
})(jQuery);