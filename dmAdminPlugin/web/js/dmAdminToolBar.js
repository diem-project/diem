(function($) {
  
$.widget('ui.dmAdminToolBar', $.extend({}, $.dm.coreToolBar, {

  _init : function()
  {
    this.initToolBar();
    
    $('div.dm_menu', this.element).disableSelection().dmMenu({
      hoverClass: 'ui-state-active'
    });

    this.element.find('a.tipable').tipsy({gravity: $.fn.tipsy.autoNorth});
  }

}));

})(jQuery);