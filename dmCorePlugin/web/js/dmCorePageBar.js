(function($)
{

  $.dm.corePageBar = {
  
    initPageBar: function()
    {
      var pageBar = this, $toggler = $('#dm_page_bar_toggler'), $tree = $('#dm_page_tree');

      $toggler.click(function()
      {
        pageBar.open();
      })
      .one('mouseover', function()
      {
        pageBar.load()
      });

      $(window).bind('resize', function()
      {
        winH = $(window).height();
        $toggler.css('top', winH / 2 - 65);
        $tree.height(winH - 50);
      }).trigger('resize');
    },
    
    load: function()
    {
      var pageBar = this;

      if (pageBar.element.hasClass('loaded')) 
      {
        return;
      }

      pageBar.element.addClass('loaded').block();

      $.ajax({
        url:      $.dm.ctrl.getHref('+/dmInterface/loadPageTree'),
        success:  function(html)
        {
          $('#dm_page_tree').hide().html(html).dmExtractEncodedAssets();
          pageBar.refresh();
          pageBar.element.unblock();
          setTimeout(function()
          {
            pageBar.loaded();
            $('#dm_page_tree').show();
          }, 50);
        }
      });
    },
    
    loaded: function()
    {

    },
    
    open: function()
    {
      var pageBar = this;
      
      pageBar.load();
      
      pageBar.element.addClass('open').outClick(function()
      {
        pageBar.close();
      });
      $('#dm_page_bar_toggler').hide();
    },
    
    close: function()
    {
      this.element.removeClass('open').outClick('remove');
      $('#dm_page_bar_toggler').show();
    },
    
    refresh: function()
    {
      var self = this;
      
      $tree = $('#dm_page_tree');

      $tree.tree(self.getTreeOptions($tree));
      
      if ($.fn.draggable) 
      {
        $tree.find('li > a').mousedown(function(ev){
          // Without it IE selects text while dragging
          if($.browser.msie)
          {
            document.onselectstart = function() { return false; }
          }
        }).draggable({
          containment: 'document',
          distance: 20,
          revert: 'invalid',
          //zIndex: 16777271, // max z-index for Safari 3 - fix for max z index
          helper: function(e)
          {
            return $('<div class="dm dm_page_draggable_helper"></div>').maxZIndex().html($(this).clone()).appendTo($('body'));
          },
          start: function(event, ui)
          {
            $('div.markItUp, input.dm_link_droppable').addClass('active');
          },
          stop: function(event, ui)
          {
            $('div.markItUp, input.dm_link_droppable').removeClass('active');
            // Re-enable text selection in IE
            if($.browser.msie)
            {
              document.onselectstart = null;
            }
          }
        });
      }
    },

    getTreeOptions: function($tree)
    {
      var rootId = $tree.find('> ul > li:first').attr('id');
      
      return this.extendTreeOptions($tree, {
        ui: {
          theme_path: $.dm.ctrl.options.dm_core_asset_root + 'lib/dmTree/',
          theme_name: 'page-panel',
          animation  : 200
        },
        types: {
          'default': {
            icon: { image:  $.dm.ctrl.options.dm_core_asset_root + 'images/16/sprite.png'},
            clickable: true,
            renameable: false,
            deletable: false,
            creatable: false,
            draggable: false,
            max_children: -1,
            max_depth: -1,
            valid_children: "all"
          },
          'manual': {
            icon: { position: '0 -864px;'}
          },
          'auto': {
            icon: { position: '0 -848px;'}
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
            EV.preventDefault(); EV.stopPropagation(); return false;
          },
          beforeclose: function(node, tree)
          {
            return node.id != rootId;
          }
        },
        opened: [rootId]
      });
    }
    
  };
  
})(jQuery);
