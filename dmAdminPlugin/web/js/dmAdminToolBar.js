(function($) {
  
$.widget('ui.dmAdminToolBar', $.extend({}, $.dm.coreToolBar, {

  _init : function()
  {
    this.initToolBar();
    
    this.initMenu();
  }

}));

})(jQuery);