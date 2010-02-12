(function($)
{

  $.widget('ui.dmAdminPageBar', $.extend({}, $.dm.corePageBar, {
  
    _init: function()
    {
      this.initPageBar();
    },
    
    getTreeOptions: function($tree)
    {
			return {
        animation: 300,
        plugins: ['ui', 'cookies', 'html_data', 'themes'],
        themes: {
          theme: "pagebar",
          dots : true,
          icons: false
        },
        ui: {
          initially_open: [$tree.find('> ul > li:first').attr('id')]
        }
      };
    }
    
  }));
  
})(jQuery);
