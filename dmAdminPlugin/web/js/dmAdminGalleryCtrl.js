(function($) {

var $gallery = $("div.dm_gallery_big"), $list = $gallery.find('ul.list'), metadata = $gallery.metadata();
    
$gallery.find('a.open_form').click(function()
{
  $gallery.find('form.dm_add_media').toggle();
});
  
$list.sortable({
  tolerance:              'pointer',
  opacity:                0.5,
  placeholder:            'ui-state-highlight',
  revert:                 true,
  scroll:                 true,
  distance:               10,
  start:                  function(e, ui)
  {
    ui.placeholder.html(ui.item.html());
  },
  stop: function()
  {
    $.ajax({
      url: $.dm.ctrl.getHref('+/dmMedia/sortGallery?model='+metadata.model+'&pk='+metadata.pk+'&'+$list.sortable('serialize'))
    });
  }
});

})(jQuery);