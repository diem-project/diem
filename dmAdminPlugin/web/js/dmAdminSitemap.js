(function($)
{

$('div.dm_sitemap_tabs').tabs({});

$('input.dm_sitemap_generate').click(function()
{
  $('form.dm_sitemap_generate_form').submit();
});

})(jQuery);