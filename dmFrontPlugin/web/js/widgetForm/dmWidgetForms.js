(function($) {

$.fn.extend({
  
  dmWidgetContentBaseMediaForm: function(widget, droppableOptions)
  {
    var self = this,
    $form = self.find('form:first'),
    formName = self.metadata().form_name;

    $form.append('<input type="hidden" name="'+formName+'[widget_width]" value="'+widget.element.width()+'" />');
  
    $('input.dm_media_receiver', $form).droppable($.extend({
      accept:       '#dm_media_bar li.file',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui) {
        $('input.dm_media_id', $form).val(ui.draggable.attr('id').replace(/dmm/, ''));
        $form.submit();
      }
    }, droppableOptions || {}));
    
    $('a.show_media_fields', $form).click(function() {
      $('ul.media_fields', $form).toggle();
    });
  },

  dmWidgetContentImageForm: function(widget)
  {
    var $form = this.find('form:first');

    this.dmWidgetContentBaseMediaForm(widget, {
      accept: '#dm_media_bar li.file.image'
    });

    $('select.dm_media_method', $form).bind('change', function() {
      $('li.background', $form)[$(this).val() == 'fit' ? 'show' : 'hide']();
    }).trigger('change');
  },
  
  dmWidgetContentTextForm: function(widget)
  {
    var $form = this.find('form:first');

    $form.find('div.dm_tabbed_form').dmCoreTabForm({});

    this.dmWidgetContentImageForm(widget);
  }
  
});

})(jQuery);