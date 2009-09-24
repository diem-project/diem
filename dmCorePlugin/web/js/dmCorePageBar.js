(function($)
{

  $.dm.corePageBar = {
  
    initPageBar: function()
    {
      var pageBar = this, $toggler = $('#dm_page_bar_toggler'), $tree = $('#dm_page_tree');
      
      $toggler.click(function()
      {
        pageBar.open();
      }).one('mouseover', function()
      {
        pageBar.load()
      });
      
      $(window).bind('resize', function()
      {
        winH = $(window).height();
        $toggler.css('top', $(window).height() / 2 - 50);
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
      pageBar.element.addClass('loaded');
      pageBar.element.block();
      $.ajax({
				dataType: 'json',
        url: $.dm.ctrl.getHref('+/dmInterface/loadPageTree'),
        success: function(data)
        {
          $.globalEval(data.js);
          $('#dm_page_tree').hide().html(data.html);
          pageBar.refresh();
          pageBar.element.unblock();
          setTimeout(function()
          {
            $('#dm_page_tree').show();
          }, 10);
        }
      });
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
      var pageBar = this;
      
      $tree = $('#dm_page_tree');
			
			$tree.tree(pageBar.getTreeOptions());
      
      if ($.fn.draggable) 
      {
        $('li', $tree).draggable({
          containment: 'document',
          distance: 20,
          revert: 'invalid',
					zIndex: 1000,
          helper: function(e)
          {
            return $('<div class="dm_page_draggable_helper">').html($(this).find('a:first').clone()).appendTo($('body'));
          },
          start: function(event, ui)
          {
            pageBar.close();
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
