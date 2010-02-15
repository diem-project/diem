(function($)
{
  $(function() {

    var $tree = $('#dm_full_page_tree');

    $tree.jstree({
      animation: 300,
      plugins: ['ui', 'html_data', 'themes', 'move', 'types'],
      themes: {
        theme: 'pagetree',
        dots : true,
        icons: false
      },
      ui: {
        initially_open: [$tree.find('> ul > li:first').attr('id')]
      },
      types : {
				// the default type
				"default" : {
					"max_children"	: -1,
					"max_depth"		: -1,
					"valid_children": "all",

					// Bound functions - you can bind any other function here (using boolean or function)
					"select_node"	: true,
					"open_node"		: true,
					"close_node"	: true,

					"create_node"	: true,
					"delete_node"	: true,
					"cut"			: true,
					"copy"			: true,
					"paste"			: true
				}
			}
    });

  });
})(jQuery);