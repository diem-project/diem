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

      $.jstree._themes = $.dm.ctrl.options.dm_core_asset_root+'lib/jstree10b2/themes/';
      
      $tree.jstree(self.getTreeOptions($tree));
      
      if ($.fn.draggable) 
      {
        $tree.find('li').draggable({
          containment: 'document',
          distance: 20,
          revert: 'invalid',
          zIndex: 1000,
          helper: function(e)
          {
            return $('<div class="dm dm_page_draggable_helper">').html($(this).find('a:first').clone()).appendTo($('body'));
          },
          start: function(event, ui)
          {
            self.close();
            $('div.markItUp, input.dm_link_droppable').addClass('active');
          },
          stop: function(event, ui)
          {
            $('div.markItUp, input.dm_link_droppable').removeClass('active');
          }
        });
      }
    }
    
  };
  
})(jQuery);
