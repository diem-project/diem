(function($)
{
  $(function() {
    // admin
    $('#dm_admin_content div.sf_widget_form_dm_tags_autocomplete select').each(function()
    {
      $(this).fcbkcomplete($.extend({
        json_url: $.dm.ctrl.getHref('+/dmTagAdmin/getTagsForAutocomplete'),
        cache: true,
        filter_case: false,
        filter_hide: true,
        newel: true
      }, $(this).metadata()));
    });
  });
  
})(jQuery);