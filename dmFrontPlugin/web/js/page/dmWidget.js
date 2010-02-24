(function($) {
  
$.widget('ui.dmWidget', {

  _init : function()
  {
    this.initialize();

    this.element.data('loaded', true);
  },

  openEditDialog: function()
  {
    var widget = this, activeTab = null, dialogClass = widget.element.attr('id')+'_edit_dialog';

	  if ($('body > div.'+dialogClass).length)
		{
      $('body > div.'+dialogClass).find('div.ui-dialog-content').dialog('moveToTop');
			return;
		}
		
    var $dialog = $.dm.ctrl.ajaxDialog({
      url:          $.dm.ctrl.getHref('+/dmWidget/edit'),
      data:         {widget_id: widget.getId()},
      title:        $('a.dm_widget_edit', widget.element).attr('original-title'),
      width:        370,
			'class':      'dm_widget_edit_dialog_wrap '+dialogClass,
      beforeClose:  function()
      {
        if (!widget.deleted)
        {
          widget.reload(500);
        }
      }
    }).bind('dmAjaxResponse', function() {
      $dialog.prepare();

      $('a.delete', $dialog).click(function()
      {
        if (confirm($(this).attr('original-title')+" ?"))
        {
          $.fn.tipsy.remove();
          widget._delete();
          $dialog.dialog('close');
        }
      });
      
			var $form = $('div.dm_widget_edit', $dialog);
			if (!$form.length)
			{
        return;
      }
      
      /*
       *Move cut & copy actions to the title
       */
      if ($cutCopy = $form.find('div.dm_cut_copy_actions').orNot())
      {
        $cutCopy.appendTo($dialog.parent().find('div.ui-dialog-titlebar')).show().find('a').click(function()
        {
          var $a = $(this).addClass('s16_gear');
          
          $.ajax({
            url:      $(this).attr('href'),
            success:  function()
            {
              $('#dm_tool_bar').dmFrontToolBar('reloadAddMenu', function()
              {
                $a.removeClass('s16_gear');
              });
            }
          });

          return false;
        });
      }
      /*
       * Apply generic front form abilities
       */
      $form.dmFrontForm();
      /*
       * Apply specific widget form abilities
       */
      if ((formClass = $form.metadata().form_class) && $.isFunction($form[formClass]))
      {
        $form[formClass](widget);
      }
      /*
       * Restore active tab
       */
      if(activeTab)
      {
        $form.find('div.dm_tabbed_form').tabs('select', activeTab);
      }
      /*
       * Enable code editor link
       */
      $form.find('a.code_editor').each(function() {
        var $this = $(this).click(function() {
          $('#dm_tool_bar').dmFrontToolBar('openCodeEditor', function($codeEditor)
          {
            $codeEditor.find('#dm_code_editor_file_open a[href='+$this.attr('href')+']').trigger('click');
          });
        });
      });

      // enable tool tips
      $dialog.parent().find('a[title], input[title]').tipsy({gravity: $.fn.tipsy.autoSouth});
      
      $form.find('form').dmAjaxForm({
        beforeSubmit: function(data) {
          $dialog.block();
          if ($tabbedFormActiveTab = $form.find('ul.ui-tabs-nav > li.ui-tabs-selected:first').orNot())
          {
            activeTab = $tabbedFormActiveTab.find('>a').attr('href');
          }
        },
        error: function(xhr, textStatus, errorThrown)
        {
          $dialog.unblock();
          widget.element.unblock();
          $.dm.ctrl.errorDialog('Error when updating the widget', xhr.responseText);
        },
        success: function(data)
        {
          if('saved' == data)
          {
            $dialog.dialog('close');
            return;
          }

          parts = data.split(/\_\_DM\_SPLIT\_\_/);

          // update widget content
          if(parts[1])
          {
            widget.replace(parts[1]);
          }

          $form.trigger('submitSuccess');

          // update dialog content
          $dialog.html(parts[0]).trigger('dmAjaxResponse');
        }
      });
    });
  },
  
  _delete: function()
  {
    var self = this;
    self.deleted = true;
    
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/delete'),
      data:     {widget_id: self.getId()}
    });
    
    self.element.slideUp(500, function() {self.destroy();self.element.remove();});
  },

  reload: function(timeout)
  {
    var self = this;

    self.element.block();

    setTimeout(function()
    {
      $.ajax({
        url:      $.dm.ctrl.getHref('+/dmWidget/getFull'),
        data:     {widget_id: self.getId()},
        success:  function(html)
        {
          self.replace(html);
        }
      });
    }, timeout || 0);
  },

  replace: function(html)
  {
    if($encodedAssets = $('>div.dm_encoded_assets', '<div>'+html+'</div>'))
    {
      this.element.append($encodedAssets).dmExtractEncodedAssets();
    }
    
    this.element
    .attr('class', $('>div:first', '<div>'+html+'</div>').attr('class'))
    .find('>div.dm_widget_inner')
    .html($('>div.dm_widget_inner', html).html())
    .attr('class', $('>div.dm_widget_inner', html).attr('class'))
    .end()
    .unblock()
    .trigger('dmWidgetLaunch');
  },

  openRecordEditDialog: function(widgetId)
  {
    var self = this, $button = self.element.find('a.dm_widget_record_edit');

    $.fn.tipsy.remove();

    $button.block();
    
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/editRecord'),
      data:     {widget_id: self.getId(), dm_embed: 1},
      success:  function(html)
      {
        $('<div class="diem-colorbox none"></div>')
        .html(html)
        .dmExtractEncodedAssets()
        .find('a')
        .colorbox({
          width:"90%",
          height:"90%",
          iframe: true,
          speed: 200,
          opacity: 0.5,
          onClosed: function()
          {
            self.reload();
          }
        })
        .trigger('click');

        $button.unblock();

        var $close = $('#cboxClose').attr('rel', ''), interval = setInterval(function()
        {
          if($close.attr('rel') == 'dm_close')
          {
            clearInterval(interval);
            $close.trigger('click');
          }
        }, 200);
      }
    });
  },
  
  initialize: function()
  {
    var self = this;
    
    this.id = this.element.attr('id').substring(10);
    
    $('a.dm_widget_edit', this.element).click(function() {
      if (!self.element.hasClass('dm_dragging')) {
        self.openEditDialog();
      }
    }).tipsy({gravity: $.fn.tipsy.autoSouth});

    $('a.dm_widget_record_edit', this.element).click(function() {
      self.openRecordEditDialog();
    }).tipsy({gravity: $.fn.tipsy.autoSouth});

    if($('div.dm_new_widget', this.element).length)
    {
      $('a.dm_widget_record_edit', this.element).hide();
    }
  },
  
  getId: function()
  {
    return this.id;
  }

});

})(jQuery);