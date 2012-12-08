(function($) {

$.widget('ui.dmArea', {

  _init : function()
  {
    this.initialize();
  },
  
  initialize: function()
  {
    this.id = this.element.attr('id').substring(8);
    
    this.initZones();
  },
  
  initZones: function()
  {
    this.$zonesContainer = $('div.dm_zones', this.element);
    
    this.$zones = $('div.dm_zone', this.$zonesContainer);

    if (this.$zones.length)
    {
      this.$zones.dmZone();
    }
    
    if (this.$zonesContainer.hasClass('ui-sortable'))
    {
      this.$zonesContainer.sortable('refresh');
    }
    else
    {
      this.$zonesContainer.sortable({
        opacity:                0.5,
        handle:                 'a.dm_zone_edit',
        distance:               5,
        placeholder:            'dm_zone_placeholder',
        revert:                 false,
        scroll:                 true,
        connectWith:            'div.dm_zones',
        forceHelperSize:        true,
        forcePlaceholderSize:   false,
        tolerance:              'pointer',
        receive:                function(e, ui) { sortEvents.receive = $(this).parent(); },
        remove:                 function(e, ui) { sortEvents.remove = true; },
        update:                 function(e, ui) { sortEvents.update = true; },
        start:                  function(e, ui)
        {
          ui.item.addClass('dm_dragging');
          
          // adding a zone
          if (ui.placeholder.is('span')) 
          {
            ui.placeholder
            .addClass('dm dm_zone block')
            .html('<a class="dm dm_zone_edit"/><div class="dm_widgets"></div>')
            .css('width', '100%');
          }
          // moving a zone
          else 
          {
            ui.placeholder.addClass(ui.item.attr('class'))
            .css({
              'width': ui.item.css('width'),
              'margin': '0'
            })
            .html(ui.item.html());
          }
          
          $('#dm_page div.dm_zones').addClass('droppable-active');
          
          sortEvents = [];
        },
        stop:                   function(e, ui)
        {
          if (sortEvents.update && sortEvents.receive && sortEvents.remove)
          {
            sortEvents.receive.dmArea('moveZone', ui.item);
          }
          else if (sortEvents.update && sortEvents.receive)
          {
            $(this).parent().dmArea('addZone');
          }
          else if (sortEvents.update)
          {
            $(this).parent().dmArea('sortZones');
          }
          
          setTimeout(function() { ui.item.removeClass('dm_dragging'); }, 200);
          
          $('#dm_page div.dm_zones').removeClass('droppable-active');
        }
      });
    }
  },
  
  moveZone: function($zone)
  {
    $.ajax({
      url: $.dm.ctrl.getHref('+/dmZone/move')
      +"?moved_dm_zone="+$zone.dmZone('getId')
      +"&to_dm_area="+this.getId()
      +"&"+$('div.dm_zones', this.element).sortable('serialize')
    });
  },
  
  addZone: function()
  {
    var area = this;
    $.ajax({
      url:      $.dm.ctrl.getHref('+/dmZone/add')+"?to_dm_area="+area.getId(),
      success:  function(zoneHtml) {
        $('div.dm_zones', area.zonesContainer).find('span.zone_add').replaceWith(zoneHtml);
        area.initialize();
        area.sortZones();
        $('body').trigger('zoneAdded');
      }
    });
  },
  
  sortZones: function()
  {
    $.ajax({
      url: $.dm.ctrl.getHref('+/dmZone/sort')+"?dm_area="+this.getId()+"&"+$('div.dm_zones', this.element).sortable('serialize')
    });
  },
  
  getZones: function()
  {
    return this.$zones;
  },
  
  getId: function()
  {
    return this.id;
  }

});

})(jQuery);