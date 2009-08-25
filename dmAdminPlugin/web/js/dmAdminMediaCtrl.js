(function($) {
  
$.dm.mediaCtrl = {

  init: function()
  {
    this.$ = $("div.dm_media_library");
    
    this.metadata = this.$.metadata();
    
    this.controls();

    this.content();
  },
  
  content: function()
  {
    var self = this,
    $content = $('ul.content', self.$);
		
    $('li.file a.link', $content).bind('click', function() {
      var $dialog = $.dm.ctrl.ajaxDialog({
        modal:  true,
        url:    $(this).attr("href"),
        class:  'dm_media_library dm_media_file_dialog',
        width:  700,
        height: 420
      }).bind('dmAjaxResponse', function() {
        $dialog.prepare();
        $form_wrap = $('div.form', $dialog);
        $dialog.dialog('option', 'title', $('.title', $dialog).text());
        $("form", $dialog).dmAjaxForm({
          beforeSubmit: function() {
            $form_wrap.block();
          },
          success:  function(data) {
            if (data.substr(0, 4) == '[OK]') {
              location.href = data.split('|')[1];
            } else {
              $form_wrap.unblock().html(data);
              $dialog.trigger('dmAjaxResponse');
            }
          }
        });
      });
      return false;
    });
    
    if (this.metadata.open_media)
    {
      $('li.file.media_id_'+this.metadata.open_media+' a.link', $content).trigger('click');
    }
  },

  controls: function()
  {
    var self = this;
    $("div.control a.dialog_me", self.$).bind('click', function() {
      var $dialog = $.dm.ctrl.ajaxDialog({
        modal:  true,
        title:  $(this).html(),
        url:    $(this).attr("href"),
        width:  380
      }).bind('dmAjaxResponse', function() {
        $dialog.prepare();
        $("form", $dialog).dmAjaxForm({
          beforeSubmit: function() {
            $dialog.block();
          },
          success:  function(data) {
            if (data.substr(0, 4) == '[OK]') {
              location.href = data.split('|')[1];
            } else {
              $dialog.unblock().html(data).trigger('dmAjaxResponse');
            }
          }
        });
      });
      return false;
    });
  }

};

$.dm.ctrl.add($.dm.mediaCtrl);

})(jQuery);