(function($)
{

  $.dm.coreMediaBar = {
  
    initMediaBar: function()
    {
      var mediaBar = this, $toggler = $('#dm_media_bar_toggler'), $browser = $('#dm_media_browser');
      
      $toggler.click(function()
      {
        mediaBar.open();
      }).one('mouseover', function()
      {
        mediaBar.load();
      });

      $(window).bind('resize', function()
      {
        winH = $(window).height();
        $toggler.css('top', winH / 2 - 65);
        $browser.height(winH - 70);
      }).trigger('resize');
    },
    
    load: function()
    {
      if (this.element.hasClass('loaded')) 
      {
        return;
      }
      this.element.addClass('loaded');
      this.reload($('#dm_media_browser').metadata().folder_id);
    },
    
    open: function()
    {
      var mediaBar = this;
      
      mediaBar.load();
      
      mediaBar.element.addClass('open').outClick(function()
      {
        mediaBar.close();
      });
      $('#dm_media_bar_toggler').hide();
    },
    
    close: function()
    {
      this.element.removeClass('open').outClick('remove');
      $('#dm_media_bar_toggler').show();
    },
    
    refresh: function()
    {
      var media = this;
      
      $('ul.content > li.folder, div.breadCrumb > a', media.element).bind('click', function()
      {
        media.reload($(this).attr('id').replace(/dmf/, ''));
      });
      
      if ($.fn.draggable && ($files = $('ul.content > li.file', media.element).orNot())) 
      {
        $files.draggable({
          containment: 'document',
          zIndex: 16777271, // max z-index for Safari 3
          helper: function()
          {
            return $('<div class="dm_media_helper file"></div>').html($(this).html()).appendTo($('body'));
          },
          revert: 'invalid'
        });
      }
    },
    
    reload: function(folderId)
    {
      var media = this;
      
      $('ul.content > li.folder, div.breadCrumb > a', media.element).unbind('click');
      
      if ($.fn.draggable && ($files = $('ul.content > li.file', media.element).orNot())) 
      {
        $files.draggable('destroy');
      }
      
      $('#dm_media_browser').block();
      
      $.ajax({
        url: $.dm.ctrl.getHref('+/dmInterface/loadMediaFolder') + '?folder_id=' + folderId,
        success: function(data)
        {
          $('#dm_media_browser').unblock().html(data);
          media.refresh();
        }
      });
    }
    
  };
  
})(jQuery);
