(function($)
{

  var $table = $('#dm_page_meta_table');

  $table.dataTable({
    "oLanguage": {
			"sUrl": $table.metadata().translation_url
		}
  });
  
})(jQuery);