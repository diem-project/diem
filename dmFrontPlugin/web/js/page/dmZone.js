(function($) {

$.widget('ui.dmZone', {

  _init : function()
  {
    var self = this;
    
    self.initialize();
  },
  
  initialize: function()
  {
    this.id = this.element.attr('id').substring(8);
    
    this.initWidgets();
    
    this.initEdit();
  },
  
  initEdit: function()
  {
    var zone = this;
    
    $('a.dm_zone_edit', this.element).bind('click', function() {
      if (zone.element.hasClass('dm_dragging')) {
        return false;
      }
      var $dialog = $.dm.ctrl.ajaxDialog({
        url:      $.dm.ctrl.getHref('+/dmZone/edit'),
        data:     { zone_id: zone.getId() },
        title:    $(this).attr('title'),
        beforeclose:  function() {
          if (zone.deleted) return;
					setTimeout(function() {
	          $.ajax({
	            url:      $.dm.ctrl.getHref('+/dmZone/getAttributes'),
	            data:     { zone_id: zone.getId() },
	            success:  function(data) {
	              datas = data.split('\_\_DM\_SPLIT\_\_');
	              zone.element.css('width', datas[0]);
	              zone.element.attr('class', 'dm_zone '+ datas[1]);
	            }
	          });
				  }, 100);
        }
      }).bind('dmAjaxResponse', function() {
        $dialog.prepare();
	      /*
	       * Apply generic front form abilities
	       */
	      $dialog.dmFrontForm();
        var $form = $('form', $dialog).dmAjaxForm({
          beforeSubmit: function() {
            $dialog.block();
            zone.element.block();
          },
          success:  function(data) {
            if (data == 'ok') {
              $dialog.dialog('close');
            }
            $dialog.html(data).trigger('dmAjaxResponse');
            if(!$('ul.error_list', $form).length) {
              zone.element.css('width', $('#dm_zone_width', $form).val());
              zone.element.attr('class', 'dm_zone '+ $('#dm_zone_css_class', $form).val());
            }
            zone.element.unblock();
          }
        });
        $('a.delete', $form).click(function() {
          if (confirm($(this).attr('title')+" ?")) {
            zone.delete();
            $dialog.dialog('close');
          }
        });
      });
    });
  },
  
  initWidgets: function()
  {
    this.$widgets = $('div.dm_widget', this.element);
    
    if (this.$widgets.length)
    {
      this.$widgets.dmWidget();
    }
    
    $('div.dm_widgets', this.element).sortable({
      opacity:                0.5,
      handle:                 'a.dm_widget_edit',
      distance:               5,
      placeholder:            'dm_widget_placeholder',
      revert:                 false,
      scroll:                 true,
      connectWith:            'div.dm_widgets',
      forceHelperSize:        false,
      forcePlaceholderSize:   false,
      tolerance:              'pointer',
      start:                  function(e, ui) { sortEvents = []; },
      receive:                function(e, ui) { sortEvents.receive = $(this).parent(); },
      remove:                 function(e, ui) { sortEvents.remove = true; },
      update:                 function(e, ui) { sortEvents.update = true; },
      start:                  function(e, ui) {
        ui.item.addClass('dm_dragging');
        ui.placeholder.addClass(ui.item.attr('class')).css('width', ui.item.css('width')).html(ui.item.html());
        sortEvents = [];
      },
      stop:                   function(e, ui) {
        if (sortEvents.update && sortEvents.receive && sortEvents.remove) {
          sortEvents.receive.dmZone('moveWidget', ui.item);
        }
        else if (sortEvents.update && sortEvents.receive) {
          $(this).parent().dmZone('addWidget', ui.item);
        }
        else if (sortEvents.update) {
          $(this).parent().dmZone('sortWidgets');
        }
        setTimeout(function() { ui.item.removeClass('dm_dragging'); }, 200);
      }
    });
  },
  
  moveWidget: function($widget)
  {
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/move')
      +"?moved_dm_widget="+$widget.dmWidget('getId')
      +"&to_dm_zone="+this.getId()
      +"&"+$('div.dm_widgets', this.element).sortable('serialize'),
    });
  },
  
  addWidget: function($widget)
  {
    zone = this;
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/add')+"?to_dm_zone="+zone.getId(),
      data: {
        mod:    $widget.attr('id').split(/_/)[1],
        act:    $widget.attr('id').split(/_/)[2]
			},
      success:  function(widgetHtml) {
        $('div.dm_widgets', zone.element).find('span.widget_add').replaceWith(widgetHtml);
        $newWidget = $('div.dm_widget:not(.loaded)', zone.element);
        zone.initialize();
        zone.sortWidgets();
        $newWidget.dmWidget('openEditDialog');
      }
    });
  },
  
  sortWidgets: function()
  {
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmWidget/sort')+"?dm_zone="+this.getId()+"&"+$('div.dm_widgets', this.element).sortable('serialize'),
    });
  },
  
  delete: function()
  {
    var zone = this;
    
    this.deleted = true;
    
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmZone/delete'),
      data:     { zone_id: this.getId() }
    });
    
    this.element.slideUp(500, function() { zone.destroy(); zone.element.remove(); });
  },
  
  getId: function()
  {
    return this.id;
  },
  
  getWidgets: function()
  {
    return this.$widgets;
  }

});

$.extend($.ui.dmZone, {
  getter: "getWidgets getId"
});

})(jQuery);