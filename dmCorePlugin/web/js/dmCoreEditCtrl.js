(function($)
{

  $.dm.coreEditCtrl = $.extend($.dm.coreCtrl, {
  
    liveEvents: function()
    {
      $('.dm_js_confirm').live('click', function(e)
      {
        e.stopPropagation();
        if (!confirm(($(this).attr('title') || 'Are you sure') + ' ?')) 
        {
          return false;
        }
      });
			
      $('div.ui-dialog a.close_dialog').live('click', function()
      {
        $(this).closest('div.ui-dialog-content').dialog('close');
      });
    },
    
    dialog: function(options)
    {
      var opt = $.extend($.ui.dialog.defaults, {
        zIndex: 100,
        dragStart: function(e)
        {
          $(e.target).hide().parent().css('opacity', 0.5);
        },
        dragStop: function(e)
        {
          $(e.target).show().parent().css('opacity', 1);
        },
        resizable: false
		  }, options || {});
      
      opt.dialogClass = opt.class ? opt.class + " dm" : "dm";
			
      var $dialog = $('<div>').dialog(opt).bind('dialogclose', function()
      {
        setTimeout(function() {
          $dialog.dialog('destroy').remove();
        }, 100);
      });
      
      $dialog.prepare = function()
      {
        $dialog.unblock().find('input[type=text], textarea').filter(':first').focus();
      };
      
      return $dialog;
    },
    
    ajaxJsonDialog: function(opt)
    {
      var self = this,
      opt = $.extend({
        title: 'Loading'
      }, opt);
      
      var $dialog = this.dialog(opt).block();
      
      $.ajax({
        url: opt.url,
        data: opt.data || {},
				dataType: 'json',
        success: function(data)
        {
					if (data.stylesheets)
					{
						$.loadStylesheets(data.stylesheets);
					}
					
          if (data.js)
          {
            $.globalEval(data.js);
          }
          
          $dialog.html(data.html).trigger('dmAjaxResponse');
        },
        error: function(xhr)
        {
          $dialog.unblock();
          $.dm.ctrl.errorDialog('Error while opening '+opt.title, xhr.responseText);
        }
      });
      
      return $dialog;
    },
    
    ajaxDialog: function(opt)
    {
      self = this;
      opt = $.extend({
        title: 'Loading'
      }, opt);
      
      var $dialog = this.dialog(opt).block();
      
      $.ajax({
        url: opt.url,
        data: opt.data ||
        {},
        success: function(data)
        {
          if (data.match(/\_\_DM\_SPLIT\_\_/)) 
          {
            var parts = data.split(/\_\_DM\_SPLIT\_\_/);
            $.globalEval(parts[1]);
            data = parts[0];
          }
          
          $dialog.html(data).trigger('dmAjaxResponse');
        },
        error: function(xhr)
        {
          $dialog.unblock();
          $.dm.ctrl.errorDialog('Error in '+file, xhr.responseText);
        }
      });
      
      return $dialog;
    },
		
		errorDialog: function(title, body, opt)
		{
			opt = $.extend({
        title:    title,
        position: [5, 5],
        buttons: {
          Close: function() { $(this).dialog('close'); }
        }
      }, opt || {});
			
			if ($existing = $('#dm_error_dialog div.dm_error'))
			{
				$existing.dialog('close');
			}
          
      $('<div class="dm_error">').html(body).dialog(opt).parent().attr('id', 'dm_error_dialog');
		}
  });
	
})(jQuery);