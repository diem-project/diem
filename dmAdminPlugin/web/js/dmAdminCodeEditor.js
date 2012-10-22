(function($)
{
  $(function()
  {
    var
    $ide = $('#dm_code_editor'),
    $tree = $('#dm_code_editor_tree_inner'),
    $editor = $('#dm_code_editor_content'),
    $tabs = $editor.find('div.dm_code_editor_content_tabs');

    function resize()
    {
      height = $(window).height();
      $tree.height(height - 110);
      $editor.find('textarea')
      .height(height - 142)
      .width($editor.width() - 30);
    }

    function loadTab(ui)
    {
      var
      $panel = $('#' + ui.panel.id),
      $tab = $(ui.tab).parent(),
      $span = $(ui.tab).find('>span>span');

      $tab.attr('title', $span.unwrap().attr('title')).tipsy({gravity: $.fn.tipsy.autoSouth});
      $span.attr('title', null);

      $(window).trigger('resize');

      $('<img class="close" width="9px" height="8px" src="' + $.dm.ctrl.options.dm_core_asset_root + 'images/cross-small.png' + '" />')
      .prependTo($tab)
      .click(function()
      {
        $tabs.tabs('remove', $tabs.find('ul.ui-tabs-nav > li').index($tab));
        $.dm.removeTipsy();
        return false;
      });

      $panel.find('textarea').dmCodeArea({
        save: function()
        {
          savePanel($panel);
          return false;
        }
      });

      $panel.find('a.save').click(function()
      {
        savePanel($panel);
      });

      $(window).scrollTop(0);
    }

    function savePanel($panel)
    {
      if (!$panel.is(':visible'))
      {
        return;
      }

      $panel.find('span.info')
      .html('Saving...')
      .removeClass('s16_error s16_tick')
      .addClass('s16_gear')
      .show()

      $.ajax({
        dataType: 'json',
        type:     'post',
        url:      $ide.metadata().save_file_url,
        data: {
          file: $panel.find('input.path').val(),
          code: $panel.find('textarea').val()
        },
        success: function(data)
        {
          $panel.find('span.info')
          .html(data.message)
          .removeClass('s16_gear')
          .toggleClass('s16_error', data.type == 'error')
          .toggleClass('s16_tick', data.type != 'error')
          .show();
        }
      });
    }

    function decodePath(path)
    {
      replacements = $ide.metadata().path_replacements;
      
      for (key in replacements)
      {
        while (path.search(key) != -1)
        {
          path = path.replace(key, replacements[key]);
        }
      }
      
      return path;
    }

    /*
     * Initialisation
     */

    resize();
    $(window).bind('resize', resize);

    $tabs.tabs({
      cache: true,
      add: function(e, ui)
      {
        $tabs.tabs('select', '#' + ui.panel.id);
      },
      load: function(event, ui)
      {
        loadTab(ui);
      }
    });

    $tree.tree({
      data: {
        async:  true,
        type:   "json",
        opts:   {
          method: "GET",
          url:    $ide.metadata().get_dir_content_url
        }
      },
      ui: {
        theme_path: $.dm.ctrl.options.dm_core_asset_root + "lib/dmTree/",
        theme_name: "code-editor"
      },
      types: {
        'default': {
          icon: {image:  $.dm.ctrl.options.dm_core_asset_root + 'images/16/sprite.png'},
          clickable: true,
          renameable: false,
          deletable: false,
          creatable: false,
          draggable: false,
          max_children: -1,
          max_depth: -1,
          valid_children: "all"
        },
        'folder': {
        },
        'folder_empty': {
          clickable: false
        },
        'file': {
        }
      },
      callback: {
        onselect: function(NODE, TREE_OBJ)
        {
          TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE);
        },
        // right click - to prevent use: EV.preventDefault(); EV.stopPropagation(); return false
        onrgtclk: function(NODE, TREE_OBJ, EV)
        {
          EV.preventDefault();EV.stopPropagation();return false;
        },
        beforedata: function(NODE, TREE_OBJ)
        {
          return {dir : $(NODE).attr("id") || '/'};
        },
        ondblclk: function(NODE, TREE_OBJ)
        {
          if ($(NODE).hasClass('readable_file'))
          {
            realPath = decodePath(NODE.id);
            
            // if file already opened, switch to its panel
            if ($existingTab = $tabs.find("li[original-title=\"" + realPath + "\"]").orNot())
            {
              $tabs.tabs("select", $existingTab.find('>a').attr('href'));
            }
            //load file from server
            else
            {
              $tabs.tabs('add', $ide.metadata().open_file_url + '?id=' + NODE.id,
                '<span title="'+realPath+'">'+$(NODE).find('a:first').text()+'</span>'
              );
            }
          }
          else
          {
            TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE);
            TREE_OBJ.select_branch.call(TREE_OBJ, NODE);
          }
        }
      }
    });
  });
})(jQuery);
