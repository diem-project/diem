(function($)
{

var $tabs = $('div.dm_charts');

$tabs.tabs($.extend({
  cache: true,
  select: function()
  {
    $tabs.block();
  },
  show: function()
  {
    $tabs.unblock();
  }
}, $tabs.metadata()));
  
})(jQuery);