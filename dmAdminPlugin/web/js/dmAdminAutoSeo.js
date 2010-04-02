(function($)
{

var $autoSeo = $('#dm_admin_content div.dm_auto_seo'), $form = $autoSeo.find('form');

$autoSeo.find('li.dm_meta_preview').each(function(index)
{
  $(this).height($('li.dm_form_element:eq('+index+')', $form).height());
});
      
$autoSeo.find('li.ui-state-default').hover(function()
{
  $(this).addClass('ui-state-hover');
},
function()
{
  $(this).removeClass('ui-state-hover');
});
    
$autoSeo.find('div.dm_variables >ul').accordion({
  collapsible: true,
  active: false
});
  
})(jQuery);