(function($) {
  
var $library = $("div.dm_media_library");

$library.find('ul.content li.file a.link').bind('click', function()
{
  var $dialog = $.dm.ctrl.ajaxDialog({
    url:      $(this).attr("href"),
    'class':  'dm_media_library dm_media_file_dialog',
    width:    700,
    height:   420
  }).bind('dmAjaxResponse', function()
  {
    $dialog.prepare();
    $formWrap = $('div.form', $dialog);
    $dialog.dialog('option', 'title', $('.title', $dialog).text());
    $("form", $dialog).dmAjaxForm({
      beforeSubmit: function()
      {
        $formWrap.block();
      },
      success:  function(data) {
        if (!data.match(/</))
        {
          $library.block();
          location.href = data;
        }
        else
        {
          $formWrap.unblock().html(data);
          $dialog.trigger('dmAjaxResponse');
        }
      }
    });
  });
  return false;
});

$library.find("div.control a.dialog_me").bind('click', function()
{
  var $dialog = $.dm.ctrl.ajaxDialog({
    modal:  $.browser.mozilla && $.browser.version == '5.0' ? false : true, 
    title:  $(this).html(),
    url:    $(this).attr("href"),
    width:  380
  }).bind('dmAjaxResponse', function()
  {
    $dialog.prepare();
    $("form", $dialog).dmAjaxForm({
      beforeSubmit: function()
      {
        $dialog.block();
      },
      success:  function(data)
      {
        if (!data.match(/</))
        {
          $library.block();
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

if ($library.metadata().open_media)
{
  $('li.file.media_id_'+$library.metadata().open_media+' a.link', $content).trigger('click');
}

})(jQuery);