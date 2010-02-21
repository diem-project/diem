(function($)
{

$.widget('ui.dmFrontToolBar', $.extend({}, $.dm.coreToolBar, {

  _init: function()
  {
    this.initToolBar();
    
    this.editToggle();
    
    this.showToolBarToggle();

    this.reloadAddMenu();
    
    this.pageEditForm();
    
    this.pageAddForm();
    
    this.codeEditor();

    this.element.find('a.tipable').tipsy({gravity: $.fn.tipsy.autoSouth});
  },
	
	initSelectCulture: function()
	{
    $('#dm_select_culture').bind('change', function() {
      location.href = $.dm.ctrl.getHref('+/dmFront/selectCulture')+'?culture='+$(this).val()+'&dm_cpi='+$.dm.ctrl.options.page_id
    });
  },
  
  pageEditForm: function()
  {
    $('a.page_edit_form', this.element).click(function()
    {
      if (!$('body > div.dm_page_edit_dialog').length) 
      {
        $dialog = $.dm.ctrl.ajaxDialog({
          title:    $(this).attr('original-title'),
          'class':  'dm_page_edit_dialog',
          url:      $(this).attr('href'),
          width:    400
        }).bind('dmAjaxResponse', function()
        {
          $dialog.dmFrontPageEditForm().prepare();
        });
      }
      return false;
    });
  },

  pageAddForm: function()
  {
    this.element.find('a.page_add_form').click(function()
    {
      if (!$('body > div.dm_page_add_dialog').length)
      {
        $dialog = $.dm.ctrl.ajaxDialog({
          title:    $(this).attr('original-title'),
          'class':  'dm_page_add_dialog',
          url:      $(this).attr('href'),
          width:    400
        }).bind('dmAjaxResponse', function()
        {
          $dialog.dmFrontPageAddForm().prepare();
        });
      }
      return false;
    });
  },
  
  codeEditor: function()
  {
		var self = this;
		
    $('a.code_editor', self.element).click(function()
    {
      self.element.dmFrontToolBar('openCodeEditor');
      return false;
    });
  },
	
	openCodeEditor: function(callback)
	{
		callback = callback || null;
		
		if ($dialog = $('body > div.dm_code_editor_dialog').orNot()) 
    {
			if ($.isFunction(callback))
			{
				callback($dialog);
			}
		}
		else
		{
			var $link = $('a.code_editor', this.element).addClass('s16_gear');
      
      $dialog = $.dm.ctrl.ajaxDialog({
        title:    $link.attr('original-title'),
        'class':  'dm_code_editor_dialog',
        width:    500,
        height:   300,
        url:      $link.attr('href')
      }).bind('dmAjaxResponse', function()
      {
        $dialog.dmFrontCodeEditor({
					callback: callback
				});
        
        $link.removeClass('s16_gear');
      });
    }
	},
  
  editToggle: function()
  {
    var self = this;

    $('a.edit_toggle', this.element).click(function()
    {
      self.activateEdit(!$(this).hasClass('s24_view_on'));
    });
  },

  activateEdit: function(activate)
  {
    var self = this, $toggle = $('a.edit_toggle', this.element);

    if($toggle.hasClass('s24_view_on') == activate)
    {
      return;
    }

    if (activate)
    {
      $toggle.addClass('s24_view_on').removeClass('s24_view_off');
      $('#dm_page').addClass('edit');

      setTimeout(function() { $('#dm_page .ui-sortable').sortable('refresh'); }, 200);
    }
    else
    {
      $toggle.addClass('s24_view_off').removeClass('s24_view_on');
      $('#dm_page').removeClass('edit');
    }

    setTimeout(function()
    {
      $.ajax({
        url: $.dm.ctrl.getHref('+/dmFront/editToggle') + "?active=" + (activate ? 1 : 0)
      });
    }, 500);
  },
  
  showToolBarToggle: function()
  {
    var self = this, $toggler = $('a.show_tool_bar_toggle', self.element), $hidables = $('#dm_page_bar, #dm_media_bar, #dm_page_bar_toggler, #dm_media_bar_toggler, #sfWebDebug');
		
		var activate = function(active)
		{
      $toggler[(active ? 'add' : 'remove')+'Class']('s16_chevron_down')[(active ? 'remove' : 'add')+'Class']('s16_chevron_up')
      self.element[(active ? 'remove' : 'add')+'Class']('hidden');
      $hidables[(active) ? 'show' : 'hide']();
      $('body').css('margin-bottom', active ? '30px' : 0);
		}
		
    if ($toggler.hasClass('s16_chevron_up')) 
    {
      activate(false);
    }
    
    $toggler.click(function()
    {
      activate(active = $toggler.hasClass('s16_chevron_up'));

      setTimeout(function() {
        $.ajax({
          url: $.dm.ctrl.getHref('+/dmFront/showToolBarToggle') + "?active=" + (active ? 1 : 0)
        });
      }, 100);
    });
  },

  reloadAddMenu: function(callback)
  {
    var self = this, $menu = self.element.find('div.dm_add_menu');

    if(!$menu.length)
    {
      return;
    }
    
    $.ajax({
      url:      $menu.metadata().reload_url,
      success:  function(html)
      {
        $menu.html(html);
        
        $actions = $menu.find('li.dm_add_menu_actions').prependTo($menu.find('ul.level1'));

        $actions.find('input.dm_add_menu_search').hint();

        $menu.find('a.tipable').tipsy({gravity: 's'});

        $menu.find('input.dm_add_menu_search').bind('keyup', function()
        {
          var term = new RegExp($.trim($(this).val()), 'i');

          if(term == '')
          {
            $menu.find(':hidden').show();
            return;
          }

          $menu.find('li.dm_droppable_widgets').each(function()
          {
            $(this).show();
            
            if($(this).find('> a').text().match(term))
            {
              $(this).find('li:hidden').show();
            }
            else
            {
              $(this).find('li').each(function()
              {
                $(this)[$(this).find('span.move').text().match(term) ? 'show' : 'hide']();
              });

              $(this)[$(this).find('li:visible').length ? 'show' : 'hide']();
            }
          });
        });

        if($newZone = $menu.find('span.zone_add').orNot())
        {
          $actions.append($newZone);
        }

        var dragStart = function()
        {
          $menu.dmMenu('close');
          self.activateEdit(true);
        };

        $menu.dmMenu({
          hoverClass: 'ui-state-active',
          open: function()
          {
            this.find('input.dm_add_menu_search').focus();
          }
        })
        .find('li.dm_droppable_widgets').disableSelection();

        // add widget
        $menu.find('span.widget_add').draggable({
          connectToSortable: 'div.dm_widgets',
          helper: function()
          {
            return $('<div class="dm"><div class="dm_widget_add_helper ui-corner-all">New '+$(this).text()+'</div></div>');
          },
          appendTo: '#dm_page',
          cursorAt: { left: 30, top: 10 },
          cursor: 'move',
          start: dragStart
        });

        // add zone
        $menu.find('span.zone_add').draggable({
          connectToSortable: 'div.dm_zones',
          helper: function()
          {
            return $('<div class="dm"><div class="dm_zone_add_helper ui-corner-all">New Zone</div></div>');
          },
          appendTo: '#dm_page',
          cursorAt: { left: 30, top: 10 },
          cursor: 'move',
          start: dragStart
        });

        // add from clipboard
        $menu.find('span.widget_paste').draggable({
          connectToSortable: 'div.dm_widgets',
          helper: function()
          {
            return $('<div class="dm"><div class="dm_widget_add_helper ui-corner-all">Paste '+$(this).text()+'</div></div>');
          },
          appendTo: '#dm_page',
          cursorAt: { left: 30, top: 10 },
          cursor: 'move',
          start: dragStart
        });

        callback && $.isFunction(callback) && callback();
      }
    })
  }
  
}));
  
})(jQuery);
