(function($)
{

  $.widget('ui.dmAdminPageBar', $.extend({}, $.dm.corePageBar, {
  
    _init: function()
    {
      this.initPageBar();
    },

    extendTreeOptions: function($tree, options)
    {
      options.selected = $tree.find('> ul > li:first').attr('id');

      return options;
    }
    
  }));
  
})(jQuery);
