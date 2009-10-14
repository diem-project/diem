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
      
      //      self.element.dialog('option', 'resizable', true);
      
      self.$dialog.bind('resize', function()
      {
				var dWidth = 42;
				$textarea = $('textarea', self.element).each(function() {
					dHeight = $(this).hasClass('dm_readonly') ? 95 : 72;
					$(this).height(self.$dialog.height() - dHeight).width(self.$dialog.width() - dWidth);
				});
      });
      
      self.element.find('div#dm_code_editor_file_open ul.level2 a').click(function(e)
      {
        var path = $(this).attr('href').replace(/#/, ''), url = $.dm.ctrl.getHref('+/dmCodeEditor/file') + '?file=' + path, package = $(this).parent().parent().parent().find('>a').text(), html = '<span title="' + path + '">' + package + '<br />' + $(this).text() + '</span>';
        
        self.$tabs.tabs('add', url, html);
        return false;
      });
      
      if ($.isFunction(self.options.callback || null)) 
      {
        self.options.callback($dialog);
      }
    },
    
    tab: function(ui)
    {
      var self = this, $panel = $('#' + ui.panel.id), $tab = $(ui.tab).parent();
      
      $tab.prepend('<img class="close" width="9px" height="8px" src="' + $.dm.ctrl.options.dm_core_asset_root + 'images/cross-small.png' + '" />');
      
      $('img.close', $tab).click(function()
      {
        self.$tabs.tabs('remove', $('ul.ui-tabs-nav > li', self.$tabs).index($tab));
        return false;
      });
      
      // resize textarea
      self.$dialog.trigger('resize');
      
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
					var html = xhr.responseText;
          var line = html.replace(/\n/g, "").replace(/(.+)on line <i>(\d+)<\/i>(.+)/, '$2');
          alert('This code contains an error line ' + line + '. You should fix it.');
        }
      });
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
      };
          }
    
  });
  
})(jQuery);
