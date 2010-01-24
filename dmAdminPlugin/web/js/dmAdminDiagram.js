(function($)
{

$('div.viewport').each(function() {

  $(this)
  .find('div.toplevel').width($(this).width()).end()
  .mapbox({
    mousewheel: true,
    layerSplit: 20
  })
  .mapbox("zoomTo", 1);

});
  
})(jQuery);