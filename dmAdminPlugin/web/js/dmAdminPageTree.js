(function($)
{
  $(function() {

    var $tree = $('#dm_full_page_tree');

    $tree.tree({
      ui: {
        theme_path: $.dm.ctrl.options.dm_core_asset_root + 'lib/dmTree/',
        theme_name: 'page-edit',
        animation	: 200
      },
      types: {
        'default': {
          icon: { image:  $.dm.ctrl.options.dm_core_asset_root + 'images/16/sprite.png'},
          clickable: true,
          renameable: true,
          deletable: true,
          creatable: false,
          draggable: true,
          max_children: -1,
          max_depth: -1,
          valid_children: "all"
        },
        'manual': {
          icon: { position: '0 -864px;'}
        },
        'auto': {
          icon: { position: '0 -848px;'},
          renameable: false,
          deletable: false,
          creatable: false,
          draggable: false
        },
        'root': {
          icon: { position: '0 -864px;'},
          renameable: true,
          deletable: false,
          creatable: false,
          draggable: false
        }
      },
      plugins: {
        
      }
    });

  });
})(jQuery);