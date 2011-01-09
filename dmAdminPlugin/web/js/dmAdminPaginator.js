(function($)
{
  $(function()
  {
	  $.dm.ctrl.admin = $.dm.ctrl.admin || {};
	  $.dm.ctrl.admin.paginator = function(){
		  $('.dm_form_pagination a').unbind('click').click(function(e){
			  $('#dm_admin_content').block();
			  var link = $(this).attr('href');
			  $.ajax({
				  url: link,
				  success: function(data){
					  $('#dm_admin_content').children('#sf_admin_container').remove();
					  $('.tipsy').remove();
					  $('#dm_admin_content').append(data);
					  $.dm.ctrl.init();
					  $.dm.ctrl.admin.paginator();
					  $('#dm_admin_content').unblock();
				  }
			  });
			  e.preventDefault();
		  });
	  }
	  $.dm.ctrl.admin.paginator();
  });
})(jQuery);