(function($)
{

$.widget('ui.dmFrontToolBar', $.extend({}, $.dm.coreToolBar, {

  _init: function()
  {
    this.initToolBar();
    
    this.initMenu();
    
    this.editToggle();
    
    this.showToolBarToggle();
    
    this.pageEditForm();
    
    this.pageAddForm();
    
    this.zoneAdd();
    
    this.widgetAdd();
    
    this.codeEditor();
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
          title:    $(this).attr('title'),
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
    $('a.page_add_form', this.element).click(function()
    {
      if (!$('body > div.dm_page_add_dialog').length) 
      {
        $dialog = $.dm.ctrl.ajaxDialog({
          title:    $(this).attr('title'),
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
			var $link = $('a.code_editor', this.element);
      
      $dialog = $.dm.ctrl.ajaxDialog({
        title:    $link.attr('title'),
        'class':  'dm_code_editor_dialog',
        width:    500,
        height:   300,
        url:      $link.attr('href')
      }).bind('dmAjaxResponse', function()
      {
        $dialog.dmFrontCodeEditor({
					callback: callback
				});
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
  
  zoneAdd: function()
  {
    var self = this;

    $('div.dm_add_menu span.zone_add', self.element).draggable({
      connectToSortable: 'div.dm_zones',
      helper: function()
      {
        return $('<div class="dm"><div class="dm_zone_add_helper ui-corner-all">New Zone</div></div>');
      },
//			helper: 'clone',
			appendTo: '#dm_page',
			cursorAt: { left: 30, top: 10 },
      cursor: 'move',
      start: function(e, ui)
      {
        $('div.dm_add_menu', self.element).dmMenu('close');
        self.activateEdit(true);
      }
    });
  },
  
  widgetAdd: function()
  {
    var self = this;
    $('div.dm_add_menu span.widget_add', self.element).draggable({
      connectToSortable: 'div.dm_widgets',
      helper: function()
      {
        return $('<div class="dm"><div class="dm_widget_add_helper ui-corner-all">New '+$(this).text()+'</div></div>');
      },
      appendTo: '#dm_page',
      cursorAt: { left: 30, top: 10 },
			cursor: 'move',
      start: function(e, ui)
      {
        $('div.dm_add_menu', self.element).dmMenu('close');
        self.activateEdit(true);
      }
    });
  },

  reloadAddMenu: function()
  {
    var self = this, $addMenu = self.element.find('div.dm_add_menu').block();
    
    $.ajax({
      url:      $addMenu.metadata().reload_url,
      success:  function(html) {
        $addMenu.html(html).unblock();
      }
    })
  }
  
}));

$.ui.dmFrontToolBar.getter = "openCodeEditor reloadAddMenu";
  
})(jQuery);