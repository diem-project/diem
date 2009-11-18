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
    
    getTreeOptions: function()
    {
      return {
        ui: {
          theme_path: $.dm.ctrl.options.dm_core_asset_root + 'lib/dmTree/',
          theme_name: 'page',
          dots: true,
          hover_mode: false,
          context: null
        },
        rules: {
          clickable: "all", // which node types can the user select | default - all
          renameable: "none", // which node types can the user select | default - all
          deletable: "none", // which node types can the user delete | default - all
          creatable: "none" // which node types can the user create in | default - all
        },
        callback: {
          ondblclk: function(NODE, TREE_OBJ)
          {
						location.href = $('a', NODE).attr('href');
          },
          onselect: function(NODE, TREE_OBJ)
          {
            TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE);
          },
          // right click - to prevent use: EV.preventDefault(); EV.stopPropagation(); return false
          onrgtclk: function(NODE, TREE_OBJ, EV)
          {
						EV.preventDefault(); EV.stopPropagation(); return false;
          }
        },
        cookies: {
          prefix: "dpt"
        },
        selected: 'dmp' + $.dm.ctrl.options.page_id
      };
    }
    
  }));
  
})(jQuery);