(function($) {
  
$.dm.auth = {

  init: function()
  {
    this.$ =  $("div.dm_auth");
    
    $('#signin_username').focus();
    
    $('input', this.$).bind('mousedown', this.send).bind('keyup', this.send);
  },
  
  send: function()
  {
    $.ajax({
      url:      $('form', this.$).attr("action"),
      mode:     'abort',
      data: {
        username: $('#signin_username').val(),
        password: Base64.encode($('#signin_password').val())
      },
      success:  function(data) {
        if (data == 'ok') {
          $('form', this.$).hide().submit();
        }
      }
    });
  }
};

$.dm.ctrl.add($.dm.auth);

})(jQuery);