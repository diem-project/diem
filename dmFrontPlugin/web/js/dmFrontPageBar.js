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
      }).dblclick(function() {
        $('body').block();
        location.href = $(this).attr('href');
      });
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
          initially_open: ['dmp' + $.dm.ctrl.options.page_id]
        }
      };
    }
    
  }));
  
})(jQuery);