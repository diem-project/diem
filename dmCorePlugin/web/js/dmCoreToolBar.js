(function($) {
  
$.dm.coreToolBar = {
  
  initToolBar: function()
  {
    if ($w3c = $('#dm_html_validate').orNot())
    {
      setTimeout(function() {
        $.ajax({
          url:      self.getHref('+/dmCore/w3cValidateHtml'),
          success:  function(data) {
            $w3c.html(data);
            $w3c.find('a.show_errors').bind('click', function() {
              $.dm.ctrl.modalDialog({
                title:  $(this).attr('title')
              }).html($w3c.find('div.html_validation_errors').html());
            });
          }
        });
      }, 500);
    }
    
    if ($tidy = $('#dm_tidy_output').orNot())
    {
      $('a.dm_tidy_output', this.element).bind('click', function() {
        $.dm.ctrl.modalDialog({
          title:  $(this).attr('title')
        }).html($tidy.html());
      });
    }
    
    $('#dm_select_culture').bind('change', function() {
      location.href = $.dm.ctrl.getHref('+/dmCore/selectCulture')+'?culture='+$(this).val()
    });
    
    $('#dm_select_theme').bind('change', function() {
      location.href = $.dm.ctrl.getHref('+/dmCore/selectTheme')+'?theme='+$(this).val()
    });
  },
  
  initMenu : function()
  {
    $('div.dm_menu', this.element).dmMenu({
      hoverClass: 'ui-state-active'
    });
  }

};

})(jQuery);