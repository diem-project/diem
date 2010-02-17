(function($) {
  
var
$console = $("#dm_console"),
$command = $("#dm_command"),
$lines = $console.find(".dm_content_command"),
$wait = $console.find(".dm_console_wait");

scroll();

$lines.bind("click", function()
{
  $command.focus();
}).trigger("click");

$("#dm_command_wrap form").ajaxForm({
  beforeSubmit: function(data)
  {
    if ($("#dm_command").val() == "") return false;

    if($("#dm_command").val() == "clear")
    {
      $lines.html('<li>&nbsp;</li>');
      $("#dm_command").val("");
      return false;
    }
    
    $("#dm_command").val("Loading...");
    return true;
  },
  success: function(data)
  {
    $lines.append(data);
    scroll();
    $("#dm_command").val("");
  }
});

function scroll()
{
  $console[0].scrollTop = 16*$console.find("li").length;
}

$(window).bind('resize', function()
{
  $console.height($(window).height() - 90);
}).trigger('resize');

})(jQuery);
