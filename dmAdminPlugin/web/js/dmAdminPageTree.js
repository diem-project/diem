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
          clickable	: true,
          renameable	: true,
          deletable	: true,
          creatable	: true,
          draggable	: true,
          max_children	: -1,
          max_depth	: -1,
          valid_children	: "all",

          icon : {
            image : false,
            position : false
          }
        }
      }
    });

  });
})(jQuery);