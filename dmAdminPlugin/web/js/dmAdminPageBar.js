(function($)
{

  $.widget('ui.dmAdminPageBar', $.extend({}, $.dm.corePageBar, {
  
    _init: function()
    {
      this.initPageBar();
    },

    extendTreeOptions: function($tree, options)
    {
      return options;
    }
    
  }));
  
})(jQuery);