(function($)
{

$('div.dm_sitemap_tabs').tabs({});

$('input.dm_sitemap_generate').unbind('click.dm').bind('click.dm', function()
{
  $('form.dm_sitemap_generate_form').submit();
});

})(jQuery);