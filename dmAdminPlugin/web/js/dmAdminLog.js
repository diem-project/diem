(function($)
{

var $tabs = $('#dm_admin_content div.dm_logs');

$tabs.block().tabs($.extend({
  cache: false,
  select: function()
  {
    $tabs.block();
  },
  load: function()
  {
    $tabs.unblock();
  }
}, $tabs.metadata()));
  
})(jQuery);