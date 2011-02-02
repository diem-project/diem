(function($)
{
  $(function()
  {
    $('#dm_full_model_tree > div').each(function() {

      var $tree = $(this);

      var rootId = $tree.find('> ul > li:first').attr('id');

      $tree.tree({
        ui: {
          theme_path: $.dm.ctrl.options.dm_core_asset_root + 'lib/dmTree/',
          theme_name: 'page-edit',
          animation  : 200
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
            icon: { position: '0 -864px;'},
            valid_children: "manual"
          },
          'auto': {
            icon: { position: '0 -848px;'},
            renameable: false,
            deletable: false,
            creatable: false,
            draggable: false,
            valid_children: "manual"
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

        },
        callback: {
          onmove: function(data)
          {
            $tree.block();

            $.ajax({
              url:        $tree.metadata().move_url,
              data: {
                model:    data.node.id.substr(3),
                from:     data.old_parent.id.substr(3),
                to:       data.parent.id.substr(3),
                previous: ($(data.node).prev().attr('id') || '').substr(3)
              },
              success:    function(data)
              {
                $.dbg(data);
                $tree.unblock();
              }
            });
          },
          onselect: function(NODE, TREE_OBJ)
          {
            TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE);
          },
          beforeclose: function(node, tree)
          {
            return node.id != rootId;
          }
        },
        opened: [rootId]
      });
    });
  });
})(jQuery);