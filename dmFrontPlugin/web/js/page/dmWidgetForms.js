(function($) {

$.fn.extend({
  
  dmWidgetContentMediaForm: function(widget)
  {
    var self = this, $form = self.find('form:first'), formName = self.metadata().form_name;

    $form.append('<input type="hidden" name="'+formName+'[widget_width]" value="'+widget.element.width()+'" />');
		
    $('input.dm_media_receiver', $form).droppable({
      accept:       '#dm_media_bar li.file',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui) {
        $('input.dm_media_id', $form).val(ui.draggable.attr('id').replace(/dmm/, ''));
        $form.submit();
      }
    });
    
    $('a.show_media_fields', $form).click(function() {
      $('ul.media_fields', $form).toggle();
    });
    
    $('select.dm_media_method', $form).bind('change', function() {
      $('li.background', $form)[$(this).val() == 'fit' ? 'show' : 'hide']();
    }).trigger('change');
  },
  
  dmWidgetContentTextForm: function(widget)
  {
    var self = this, $form = self.find('form:first'), $tabs = $form.find('div.dm_tabbed_form').dmCoreTabForm({});
		self.dmWidgetContentMediaForm(widget);
  }
  
});

})(jQuery);