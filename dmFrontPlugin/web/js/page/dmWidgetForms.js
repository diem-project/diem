(function($) {

$.fn.extend({
  
  dmWidgetContentMediaForm: function(widget)
  {
    var self = this, $form = self.find('form:first');
		
    $form.append('<input type="hidden" name="dm_widget_width" value="'+widget.element.width()+'" />');
		
    $('input.dm_media_receiver', self).droppable({
      accept:       '#dm_media_bar li.file',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui) {
        $('input#dm_widget_content_media_form_mediaId', self).val(ui.draggable.attr('id').replace(/dmm/, ''));
        $form.submit();
      }
    });
    
    $('a.show_media_fields', self).click(function() {
      $('ul.media_fields', self).toggle(500);
    });
    
    $('select#dm_widget_content_media_form_method', self).bind('change', function() {
      $('li.background', self)[$(this).val() == 'fit' ? 'show' : 'hide']();
    }).trigger('change');
  },
	
	dmWidgetContentLinkForm: function(widget)
	{
		
	}
  
});

})(jQuery);