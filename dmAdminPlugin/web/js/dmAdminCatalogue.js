(function($) {

  var $list = $('.sf_admin_list'); var $content = $('#dm_admin_content');

  $('.dm_import_link', $list).bind('click', function(ev)
  {
    var $dialog = $.dm.ctrl.ajaxDialog({
      modal:  $.browser.mozilla && $.browser.version == '5.0' ? false : true, 
      title:  $(this).attr('original-title'),
      url:    $(this).attr("href"),
      width:    300
    }).bind('dmAjaxResponse', function()
    {
      $dialog.prepare();
      $("form", $dialog).dmAjaxForm({
        beforeSubmit: function()
        {
          $dialog.block();
        },
        success:  function(data) {
          if (!data.match(/</))
          {
            $content.block();
            location.href = data;
          }
          else
          {
            $dialog.unblock().html(data).trigger('dmAjaxResponse');
          }
        }
      });
    });
  
    return false;
  });
  
})(jQuery);