(function($)
{

$('#dm_admin_content img.panview').each(function() {

  var $img = $(this);
  $img.panView($img.parent().width(), $img.parent().height());

});
  
})(jQuery);