(function($)
{
  $(function() {

    var $tree = $('#dm_full_page_tree');

    $tree.tree({
      ui: {
        theme_name: 'dm_page_tree'
      },
      types: {
        "default" : {
          clickable	: false,
          renameable	: true,
          deletable	: true,
          creatable	: true,
          draggable	: true,
          max_children	: -1,
          max_depth	: -1,
          valid_children	: "all",

          icon : {
            image: $.dm.ctrl.options.dm_core_asset_root+'images/16/sprite.png',
            position: '0 -848px;'
          }
        },
        "auto" : {
          clickable	: false,
          renameable	: true,
          deletable	: true,
          creatable	: true,
          draggable	: true,
          max_children	: -1,
          max_depth	: -1,
          valid_children	: "all",

          icon : {
            image: $.dm.ctrl.options.dm_core_asset_root+'images/16/sprite.png',
            position: '0 -848px;'
          }
        },
        "manual" : {
          clickable	: false,
          renameable	: true,
          deletable	: true,
          creatable	: true,
          draggable	: true,
          max_children	: -1,
          max_depth	: -1,
          valid_children	: "all",

          icon : {
            image: $.dm.ctrl.options.dm_core_asset_root+'images/16/sprite.png',
            position: '0 -848px;'
          }
        }
      }
    });

  });
})(jQuery);