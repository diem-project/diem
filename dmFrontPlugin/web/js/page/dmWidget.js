(function($) {
  
$.widget('ui.dmWidget', {

  _init : function()
  {
    this.initialize();

    this.element.data('loaded', true);
  },

  openEditDialog: function()
  {
    var widget = this, activeTab = null, dialogClass = 'dm_widget_edit_dialog_wrap '+widget.element.attr('id')+'_edit_dialog';
		
	  if ($('body > div.'+dialogClass).length)
		{
			return;
		}
		
    var $dialog = $.dm.ctrl.ajaxDialog({
      url:          $.dm.ctrl.getHref('+/dmWidget/edit'),
      data:         { widget_id: widget.getId() },
      title:        $('a.dm_widget_edit', widget.element).attr('title'),
      width:        370,
			'class':      dialogClass,
      beforeClose:  function()
      {
        if (!widget.deleted)
        {
          widget.reload();
        }
      }
    }).bind('dmAjaxResponse', function() {
      $dialog.prepare();
			var $form = $('div.dm_widget_edit', $dialog);
			if ($form.length)
			{
        /*
         *Move cut & copy actions to the title
         */
        if ($cutCopy = $form.find('div.dm_cut_copy_actions').orNot())
        {
          $dialog.parent().find('div.ui-dialog-titlebar').append($cutCopy);
          $cutCopy.find('a').click(function() {
            $.ajax({
              url:      $(this).attr('href'),
              success:  function()
              {
                $('#dm_tool_bar').dmFrontToolBar('reloadAddMenu');
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
				/*
				 * Tell the server the form is submitted with an xhr request
				 * ( usefull when uploading files )
				 */
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
			}
      
      $('a.delete', $dialog).click(function() {
        if (confirm($(this).attr('title')+" ?")) {
          widget._delete();
          $dialog.dialog('close');
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
      data:     { widget_id: self.getId() }
    });
    
    self.element.slideUp(500, function() { self.destroy(); self.element.remove(); });
  },

  reload: function()
  {
    var self = this;

    self.element.block();
    
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/getFull'),
      data:     { widget_id: self.getId() },
      success:  function(html)
      {
        self.replace(html);
      }
    });
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
  
  initialize: function()
  {
    var widget = this;
    
    this.id = this.element.attr('id').substring(10);
    
    $('a.dm_widget_edit', this.element).click(function() {
      if (widget.element.hasClass('dm_dragging')) {
        return false;
      }
      widget.openEditDialog();
      return true;
    });
  },
  
  getId: function()
  {
    return this.id;
  }

});

$.extend($.ui.dmWidget, {
  getter: "getId openEditDialog"
});

})(jQuery);