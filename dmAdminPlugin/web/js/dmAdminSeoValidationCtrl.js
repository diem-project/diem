(function($) {
  
$.dm.seoValidationCtrl = {

  init: function()
  {
    this.$ = $("div.seo_validation");
    
    this.toggle();
  },
  
  toggle: function()
  {
    $('.dm_toggler', this.$).each(function() {
      if($(this).parent().find('.dm_toggled').orNot()) {
        $(this).toggle(function() {
          $(this).addClass('s16_bottom').removeClass('s16_next').parent().addClass('open');
        }, function() {
          $(this).removeClass('s16_bottom').addClass('s16_next').parent().removeClass('open');
        });
      }
    });
  }
  
};

dm.ctrl.registerController(dm.seoValidationCtrl);

})(jQuery);