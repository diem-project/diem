(function($)
{

  $.widget('ui.dmFrontCodeEditor', {
  
    _init: function()
    {
      var self = this;
      
      self.element.css('overflow', 'hidden');
      
      $('#dm_code_editor_file_open', self.element).css({
        height: (self.element.height() - 20) + 'px',
        overflowY: 'auto'
      });
      
      self.$tabs = self.element.find('div.dm_code_editor').tabs({
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
      
      self.$dialog = self.element.parent();
      
      self.$dialog.bind('resize', function()
      {
        $('textarea', self.element).each(function() {
          $(this).height(self.$dialog.height() - 80).width(self.$dialog.width() - 38);
        });
      });
      
      self.element.find('div#dm_code_editor_file_open ul.level2 a').click(function(e)
      {
        var path = $(this).attr('href').replace(/#/, ''), url = $.dm.ctrl.getHref('+/dmCodeEditor/file') + '?file=' + path, html = '<span title="' + path + '">' + $(this).parent().parent().parent().find('>a').text() + '<br />' + $(this).text() + '</span>';
        
        self.$tabs.tabs('add', url, html);
        return false;
      });

      self.$dialog.find('a[title]').tipsy({gravity: $.fn.tipsy.autoSouth});
      
      if ($.isFunction(self.options.callback || null)) 
      {
        self.options.callback($dialog);
      }
    },
    
    tab: function(ui)
    {
      var self = this,
      $panel = $('#' + ui.panel.id),
      $tab = $(ui.tab).parent(),
      $span = $(ui.tab).find('>span>span');

      $tab.attr('title', $span.unwrap().attr('title')).tipsy({gravity: $.fn.tipsy.autoSouth});
      $span.attr('title', null);
      
      $tab.prepend('<img class="close" width="9px" height="8px" src="' + $.dm.ctrl.options.dm_core_asset_root + 'images/cross-small.png' + '" />');
      
      $('img.close', $tab).click(function()
      {
        self.$tabs.tabs('remove', $('ul.ui-tabs-nav > li', self.$tabs).index($tab));
        $.fn.tipsy.remove();
        return false;
      });
      
      // resize textarea
      self.$dialog.trigger('resize');

      setTimeout(function()
      {
        $panel.find('textarea').dmCodeArea({
          save: function()
          {
            $panel.find('a.save').trigger('click');
            return false;
          }
        });
      }, 50);
      
      $panel.find('a.close').click(function()
      {
        self.$tabs.tabs('remove', self.$tabs.tabs('option', 'selected'));
      })
      .end()
      .find('a.save').click(function()
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
          
          if (data.type == 'css') 
          {
            self.updateCss(data.path);
          }
          else 
            if (data.type == 'php') 
            {
              self.updateWidgets(data.widgets);
            }
        },
        error: function(xhr)
        {
          $panel.unblock();

          $.dm.ctrl.errorDialog('Error in '+file, xhr.responseText);
        }
      });

      return true;
    },
    
    updateCss: function(path)
    {
      if ($css = $('link[rel=stylesheet][href*=' + path + ']').orNot()) 
      {
        $("head").append('<link rel="stylesheet" href="' + path + '?_=' + Math.floor(999999 * Math.random()) + '">');
        $css.remove();
      }
    },
    
    updateWidgets: function(widgets)
    {
      for (var id in widgets) 
      {
        $('#dm_widget_' + id + ' div.dm_widget_inner').html(widgets[id]);
      }
    }
    
  });
  
})(jQuery);
