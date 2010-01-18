(function($)
{

  $('#dm_page div.dm_pager_ajax_links a.link').live('click', function()
  {
    $(this).closest('div.dm_widget_inner').load($(this).metadata().href);
    return false;
  });

}(jQuery));