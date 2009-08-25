(function($)
{

  $.dm.coreEditCtrl = $.extend($.dm.coreCtrl, {
  
    liveEvents: function()
    {
      $('a.confirm_me').live('click', function(e)
      {
        e.stopPropagation();
        if (!confirm(($(this).attr('title') || 'Are you sure') + " ?")) 
        {
          return false;
        }
      });
      $('div.ui-dialog a.close_dialog').live('click', function()
      {
        $(this).closest('div.ui-dialog-content').dialog('close');
      });
      $('input.hint').hint();
    },
    
    dialog: function(options)
    {
      var opt = $.extend($.ui.dialog.defaults, {
        zIndex: 100,
        dragStart: function(e, ui)
        {
          ui.helper.find('div.ui-dialog-content').hide().end().css('opacity', 0.5);
        },
        dragStop: function(e, ui)
        {
          ui.helper.css('opacity', 1).find('div.ui-dialog-content').show();
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
        error: function(data)
        {
          $dialog.unblock().html(data);
        }
      });
      
      return $dialog;
    },
    
    flashMessages: function()
    {
      $("#flash").click(function()
      {
        $(this).remove();
      });
    }
  });
	
})(jQuery);