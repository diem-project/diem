(function($) {
  
$.widget('ui.dmWidget', {

  _init : function()
  {
    this.initialize();

    this.element.data('loaded', true);
  },

  openEditDialog: function()
  {
    var widget = this, activeTab = null, dialog_class = widget.element.attr('id')+'_edit_dialog';
		
	  if ($('div.'+dialog_class).length)
		{
			return;
		}
		
    var $dialog = $.dm.ctrl.ajaxJsonDialog({
      url:          $.dm.ctrl.getHref('+/dmWidget/edit'),
      data:         { widget_id: widget.getId() },
      title:        $('a.dm_widget_edit', widget.element).attr('title'),
      width:        370,
			class:        dialog_class,
      beforeclose:  function() {
        if (widget.deleted) return;
        $.ajax({
					dataType: 'json',
          url:      $.dm.ctrl.getHref('+/dmWidget/getInner'),
          data:     { widget_id: widget.getId() },
          success:  function(data) {
            widget.element.attr('class', data.widget_classes[0])
						.find('div.dm_widget_inner')
						.attr('class', data.widget_classes[1])
						.html(data.widget_html);
          }
        });
      }
    }).bind('dmAjaxResponse', function() {
      $dialog.prepare();
			var $form = $('div.dm_widget_edit', $dialog);
			if ($form.length)
			{
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
		    if ($codeEditorLink = $form.find('a.code_editor').orNot())
		    {
					$codeEditorLink.click(function() {
			      $('#dm_tool_bar').dmFrontToolBar('openCodeEditor', function($codeEditor)
						{
							$codeEditor.find('#dm_code_editor_file_open a[href='+$codeEditorLink.attr('href')+']').trigger('click');
						});
				  });
		    }
				
	      $form.find('form').dmAjaxForm({
					dataType: 'json',
	        beforeSubmit: function(data) {
	          $dialog.block();
	          widget.element.block();
						if ($tabbedFormActiveTab = $form.find('ul.ui-tabs-nav > li.ui-tabs-selected:first').orNot())
						{
							activeTab = $tabbedFormActiveTab.find('>a').attr('href');
						}
	        },
	        success:  function(data)
					{
	          if (data.type == 'close') {
	            $dialog.dialog('close');
	            widget.element.unblock();
							return;
	          }
						
	          if (data.widget_html)
						{
              widget.element
							.attr('class', data.widget_classes[0])
              .find('div.dm_widget_inner')
							.attr('class', data.widget_classes[1])
	            .html(data.widget_html);
	          }
						
	          widget.element.unblock();
	          $dialog.html(data.html).trigger('dmAjaxResponse');
	        }
	      });
			}
      $('a.delete', $dialog).click(function() {
        if (confirm($(this).attr('title')+" ?")) {
          $dialog.dialog('close');
          widget.delete();
        }
      });
    });
  },
  
  delete: function()
  {
    var widget = this;
    this.deleted = true;
    
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/delete'),
      data:     { widget_id: this.getId() }
    });
    
    this.element.slideUp(500, function() { widget.destroy(); widget.element.remove(); });
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