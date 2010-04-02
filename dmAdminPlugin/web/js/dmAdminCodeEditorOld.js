(function($)
{
  $.dm.codeEditor = {
  
    init: function()
    {
      var self = this;
      
      self.$contentFile = $('#dm_code_editor_content');
      self.$tabs = $("div.code_editor_content_tabs", self.$contentFile);
      
      self.tabs();
      self.tree();
      
      $(window).bind('resize', self.resize);
      
      self.resize();
    },
    
    resize: function()
    {
      var winH = $(window).height();
      $('#dm_code_editor_tree').height(winH - 70);
      $('#dm_code_editor_content textarea').height(winH - 130);
    },
    
    tabs: function()
    {
      var self = this;
      
      self.$tabs.tabs({
        cache: true,
        add: function(e, ui)
        {
          self.$tabs.tabs('select', '#' + ui.panel.id);
        },
        load: function(event, ui)
        {
          self.tab(ui);
        }
      });
    },
    
    tab: function(ui)
    {
      var self = this, $panel = $('#' + ui.panel.id), $tab = $(ui.tab).parent();
      
      // adjust textarea's height according to window's height
      self.resize();
      
      $tab.prepend('<img class="close" width="9px" height="8px" src="' + $.dm.ctrl.options.dm_core_asset_root + 'images/cross-small.png' + '" />');
      
      $('img.close', $tab).click(function()
      {
        self.$tabs.tabs('remove', $('ul.ui-tabs-nav > li', self.$tabs).index($tab));
        return false;
      });
      
      $panel.find('textarea').dmCodeArea({
        save: function()
        {
          $panel.find('a.save').trigger('click');
          return false;
        }
      });
      
      $panel.find('a.close').click(function()
      {
        self.$tabs.tabs('remove', self.$tabs.tabs('option', 'selected'));
      });
      
      $panel.find('a.save').click(function()
      {
        self.save($panel);
      });
    },
    
    save: function($panel)
    {
      if (!$panel.is(':visible')) 
      {
        return false;
      }
      
      $panel.block();
      var file = $panel.find('input.path').val(), self = this;
      $.ajax({
        dataType: 'json',
        type: 'post',
        url: $.dm.ctrl.getHref('+/dmCodeEditor/save'),
        data: {
          file: file,
          code: $panel.find('textarea').val()
        },
        success: function(data)
        {
          $panel.find('span.info').html(data.message)[(data.type == 'error' ? 'add' : 'remove') + 'Class']('error');
          $panel.unblock();
        }
      });
    },
    tree: function()
    {
      var self = this;
      
      $('#dm_code_editor_tree').tree({
        data: {
          type: "json",
          json: $.dm.ctrl.options.dm_tree_json,
          url: $.dm.ctrl.getHref('+/dmCodeEditor/constructTree'),
          async: true,
          async_data: function(NODE)
          {
            return {
              id: $(NODE).attr("id") || 0
            }
          }
        },
        ui: {
          theme_path: $.dm.ctrl.options.dm_core_asset_root + "lib/dmTree/",
          theme_name: "file",
          context: [{
            id: "new_dir",
            label: "New dir",
            icon: "create_dir.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('root')) 
              {
                return TREE_OBJ.check("creatable", NODE);
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              TREE_OBJ.create(false, TREE_OBJ.get_node(NODE));
              $('li.last.leaf span.clicked').parent().addClass('new_dir');
              $('li.last.leaf span.clicked input').attr('value', 'New dir');
              $('li.last.leaf a.clicked').addClass('dir');
              $('li.last.leaf a.clicked').html('New dir');
            }
          }, {
            id: "new_file",
            label: "New file",
            icon: "create.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('root')) 
              {
                return TREE_OBJ.check("creatable", NODE);
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              TREE_OBJ.create(false, TREE_OBJ.get_node(NODE));
              $('li.last.leaf span.clicked').parent().addClass('new_file');
              $('li.last.leaf span.clicked input').attr('value', 'New file');
              $('li.last.leaf a.clicked').addClass('file');
              $('li.last.leaf a.clicked').html('New file');
            }
          }, "separator", {
            id: "rename",
            label: "Rename",
            icon: "rename.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('root')) 
              {
                return -1;
              }
              
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('writable_file')) 
              {
                return TREE_OBJ.check("renameable", NODE);
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              TREE_OBJ.rename();
            }
          }, "separator", {
            id: "copy",
            label: "Copy",
            icon: "copy.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('root')) 
              {
                return -1;
              }
              
              if (NODE.children().hasClass('readable_dir') || NODE.children().hasClass('readable_file')) 
              {
                return true;
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              $.ajax({
                url: $.dm.ctrl.getHref('+/dmCodeEditor/copyCut'),
                data: {
                  id: $(NODE).attr('id'),
                  is_cut: 'copy'
                },
                success: function(data)
                {
                  if (data.substr(0, 4) == '[KO]') 
                  {
                    $.jGrowl(data.split('|')[1], {
                      theme: 'error'
                    });
                    $.tree_reference('dm_code_editor_tree').refresh();
                  }
                  else 
                  {
                    $('#_-SLASH-_').addClass('paste_true');
                    $.tree_reference('dm_code_editor_tree').copy();
                  }
                }
              });
            }
          }, {
            id: "cut",
            label: "Cut",
            icon: "cut.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('root')) 
              {
                return -1;
              }
              
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('writable_file')) 
              {
                return true;
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              $.ajax({
                url: $.dm.ctrl.getHref('+/dmCodeEditor/copyCut'),
                data: {
                  id: $(NODE).attr('id'),
                  is_cut: 'cut'
                },
                success: function(data)
                {
                  if (data.substr(0, 4) == '[KO]') 
                  {
                    $.jGrowl(data.split('|')[1], {
                      theme: 'error'
                    });
                    $.tree_reference('dm_code_editor_tree').refresh();
                  }
                  else 
                  {
                    $('#_-SLASH-_').addClass('paste_true');
                    $.tree_reference('dm_code_editor_tree').cut();
                  }
                }
              });
            }
          }, {
            id: "paste",
            label: "Paste",
            icon: "create.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('root')) 
              {
                if ($('#_-SLASH-_').hasClass('paste_true')) 
                {
                  return true;
                }
                else 
                {
                  return false;
                }
              }
              else 
              {
                return -1;
              }
              
            },
            action: function(NODE, TREE_OBJ)
            {
              $.ajax({
                url: $.dm.ctrl.getHref('+/dmCodeEditor/paste'),
                data: {
                  id: $(NODE).attr('id')
                },
                success: function(data)
                {
                  if (data.substr(0, 4) == '[KO]') 
                  {
                    $.jGrowl(data.split('|')[1], {
                      theme: 'error'
                    });
                    $.tree_reference('dm_code_editor_tree').refresh();
                  }
                  else 
                  {
                    $('#_-SLASH-_').removeClass('paste_true');
                    $.tree_reference('dm_code_editor_tree').refresh();
                  }
                }
              });
            }
          }, "separator", {
            id: "delete",
            label: "Delete",
            icon: "remove.png",
            visible: function(NODE, TREE_OBJ)
            {
              if (NODE.children().hasClass('root')) 
              {
                return -1;
              }
              
              if (NODE.children().hasClass('writable_dir') || NODE.children().hasClass('writable_file')) 
              {
                var ok = true;
                $.each(NODE, function()
                {
                  if (TREE_OBJ.check("deletable", this) == false) 
                  {
                    ok = false;
                  }
                });
                return ok;
              }
              else 
              {
                return -1;
              }
            },
            action: function(NODE, TREE_OBJ)
            {
              $.each(NODE, function()
              {
                TREE_OBJ.remove(this);
              });
            }
          }]
        },
        callback: {
          beforedelete: function(NODE, TREE_OBJ)
          {
            $name = $(NODE).children().html();
            
            if (!confirm("Do you really want to delete " + $name + " ?")) 
              return false;
            else 
              return true;
          },
          ondelete: function(NODE, TREE_OBJ, RB)
          {
            $.ajax({
              url: $.dm.ctrl.getHref('+/dmCodeEditor/delete'),
              data: {
                id: NODE.id
              },
              success: function(data)
              {
                if (data.substr(0, 4) == '[KO]') 
                {
                  $.jGrowl(data.split('|')[1], {
                    theme: 'error'
                  });
                  $.tree_reference('dm_code_editor_tree').refresh();
                }
                else 
                {
                  $.jGrowl(data.split('|')[1], {
                    theme: 'valid'
                  });
                }
              }
            });
          },
          ondblclk: function(NODE, TREE_OBJ)
          {
            var $element_dbl_click = $(NODE).find('a:first');
            
            if ($element_dbl_click.hasClass('readable_file')) 
            {
              // if file already opened, switch to its panel
              if ($("a[href$=#file_" + NODE.id + "]", self.$tabs).length) 
              {
                self.$tabs.tabs("select", "#file_" + NODE.id);
              }
              //load file from server
              else 
              {
                self.$tabs.tabs('add', $.dm.ctrl.getHref('+/dmCodeEditor/openFile') + '?id=' + NODE.id,
                  '<span title="'+self.decodeUrlTree(NODE.id)+'">'+$element_dbl_click.text()+'</span>'
                );
              }
            }
            else 
            {
              TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE);
              TREE_OBJ.select_branch.call(TREE_OBJ, NODE);
            }
          },
          onrename: function(n, l, t, r)
          {
          
            $new_basename_id = $(n).children("a:visible").text();
            $create = "";
            if (n.id == "") 
            {
              $parent = $(n).parent().parent();
              n.id = $parent.attr('id') + '_-SLASH-_' + $.dm.codeEditor.encodeUrlTree($new_basename_id);
              $('#' + n.id).attr('id', n.id);
              if ($('#' + n.id).hasClass('new_file')) 
              {
                $create = "file";
              }
              else 
              {
                $create = "dir";
              }
            }
            if (n.id != "") 
            {
              $.ajax({
                url: $.dm.ctrl.getHref('+/dmCodeEditor/renameOrCreate'),
                data: {
                  data: $(n).children("a:visible").text(),
                  id: n.id,
                  create: $create
                },
                success: function(data)
                {
                  if (data.substr(0, 4) == '[KO]') 
                  {
                    $.jGrowl(data.split('|')[1], {
                      theme: 'error'
                    });
                    $.tree_reference('dm_code_editor_tree').refresh();
                  }
                  else 
                  {
                    $decode_old_id = $.dm.codeEditor.decodeUrlTree(n.id);
                    $decode_old_basename_id = $.dm.codeEditor.basename($decode_old_id);
                    $encode_old_basename_id = $.dm.codeEditor.encodeUrlTree($decode_old_basename_id);
                    
                    $encode_new_basename_id = $.dm.codeEditor.encodeUrlTree($new_basename_id);
                    $new_id = n.id.replace($encode_old_basename_id, $encode_new_basename_id);
                    $('#' + n.id).attr('id', $new_id);
                    
                    if ($('#' + n.id).children().attr('class').search('file_')) 
                    {
                      old_ext_children = $decode_old_basename_id.substr($.dm.codeEditor.strrpos($decode_old_basename_id, '.') + 1);
                      new_ext_children = $new_basename_id.substr($.dm.codeEditor.strrpos($new_basename_id, '.') + 1);
                      children = $('#' + n.id).children();
                      children.removeClass('file_' + old_ext_children);
                      children.addClass('file_' + new_ext_children);
                    }
                    
                    $.jGrowl(data.split('|')[1], {
                      theme: 'valid'
                    });
                  }
                }
              });
              if ($create == 'file' || $create == 'dir') 
              {
                $.tree_reference('dm_code_editor_tree').refresh();
              }
            }
          }
        }
      });
    },
    /*
     * Decode Url Tree
     * exemple : $.dm.codeEditor.decodeUrlTree(_-SLASH-_data_-SLASH-_exemple_-DOT-_txt) = /data/exemple.txt
     */
    decodeUrlTree: function(id)
    {
      while (id.search("_-SLASH-_") != -1) 
        id = id.replace('_-SLASH-_', "/");
      while (id.search("_-SPACE-_") != -1) 
        id = id.replace('_-SPACE-_', " ");
      while (id.search("_-DOT-_") != -1)
        id = id.replace('_-DOT-_', ".");
      return id;
    },
    
    /*
     * Encode Url Tree
     * exemple : $.dm.codeEditor.encodeUrlTree(/data/exemple.txt) = _-SLASH-_data_-SLASH-_exemple_-DOT-_txt
     */
    encodeUrlTree: function(id)
    {
      while (id.search("/") != -1) 
        id = id.replace("/", '_-SLASH-_');
      while (id.search(" ") != -1) 
        id = id.replace(" ", '_-SPACE-_');
      while (id.indexOf(".") != -1) 
        id = id.replace('.', '_-DOT-_');
      return id;
    },
    
    
    /*
     * basename
     * @see http://fr.php.net/manual/en/function.basename.php
     */
    basename: function(name)
    {
      if (name.search("/") == 1) 
      {
        return name.match(/.*\//);
      }
      else 
      {
        return name.replace(/.*\//, "");
      }
    },
    
    /*
     * strrpos
     * @see http://fr.php.net/manual/en/function.strrpos.php
     */
    strrpos: function(haystack, needle, offset)
    {
      var i = (haystack + '').lastIndexOf(needle, offset); // returns -1
      return i >= 0 ? i : false;
    }
    
  };
  
  $.dm.codeEditor.init();
})(jQuery);