(function($)
{
  var $dom = $('#dm_admin_content div.dm_user_my_account');

  $dom.find('.collapsible_content').hide();

  $dom.find('a.collapsible_button').click(function()
  {
    $(this).parent().find('.collapsible_content').toggle();
  });

})(jQuery);