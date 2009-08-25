(function($) {
  
$.dm.console = {

  init : function()
  {
    this.$ = $("#dm_console");
    
    var $command = $("#dm_command"),
  		$lines = $(".dm_content_command",  this.$),
  		$wait = $(".dm_console_wait")
  		self = this;
		
		this.scroll();
		
		$lines.bind("click", function()
		{
		  $command.focus();
		}).trigger("click");
		
		$("#dm_command_wrap form").ajaxForm({
			beforeSubmit: function(data) {
        if ($("#dm_command").val() == "") return false;
        
        if($("#dm_command").val() == "clear")
        {
          $lines.html('<li>&nbsp;</li>');
          $("#dm_command").val("");
          return false;
        }
				$("#dm_command").val("Loading...");
      },
      success: function(data) {
        $lines.append(data);
        $.dm.console.scroll();
				$("#dm_command").val("");
      }
		});
  },
  scroll : function()
  {
    this.$[0].scrollTop = 16*$("li", this.$).length;
  }
};

$.dm.ctrl.add($.dm.console);

})(jQuery);
