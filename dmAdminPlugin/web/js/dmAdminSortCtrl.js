(function($) {
  
$('div.dm_sort ol.objects').sortable({
  containment:            'document',
  opacity:                0.5,
  placeholder:            'ui-state-highlight',
  revert:                 true,
  scroll:                 true,
  beforeStop:             function(e, ui, o)
  {
    $('li.object.active', $(this)).removeClass('active');
    ui.helper.addClass('active');
  },
  stop:                   function(event, ui)
  {
    $('li.object', $(this)).each(function(index) {
      $('>input', $(this)).val(index+1);
    });
  }
});

})(jQuery);