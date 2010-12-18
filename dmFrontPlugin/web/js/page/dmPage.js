(function($) {

$.widget('ui.dmPage', {

  _init : function()
  {
    this.initialize();
  },
  
  initialize: function()
  {
    this.$areas = $('.dm_area', this.element);
    
    this.$areas.dmArea();
  },
  
  getAreas: function()
  {
    return this.$areas;
  },
  
  getWidgetByPos: function(x, y)
  {
    var $founded = null;
    $("div.dm_widget", this.element).each(function() {
      var $widget = $(this), a = $widget.offset(), b = { top: a.top + $widget.outerHeight(), left: a.left + $widget.outerWidth() };
      if (y > a.top && y < b.top && x > a.left && x < b.left) {
        $founded = $widget;
      }
    });
    
    return $founded;
  }

});

})(jQuery);