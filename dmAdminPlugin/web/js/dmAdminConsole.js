(function($) {

  var
  $console = $("#dm_console"),
  $command = $("#dm_command"),
  $lines = $console.find(".dm_content_command"),
  commandHistory = [],
  historyStatus = {
    hasBrowsed: false,
    currentIndex: 0
  },
  $wait = $console.find(".dm_console_wait");

  scroll();

  $console.bind("click", function()
  {
    $command.focus();
  }).trigger("click");
  $('#dm_command').live('keydown', function(e) {
    if (e.which!=38 && e.which != 40) {
      return;
    }
    var $self = $(this);
    if (!historyStatus.hasBrowsed) {
      commandHistory.push($self.val());
      historyStatus.hasBrowsed=true;
      historyStatus.currentIndex = historyStatus.currentIndex+1;
    }
    if (e.which==38) {
      historyStatus.currentIndex = historyStatus.currentIndex-1;
    }
    if (e.which == 40) {
      historyStatus.currentIndex = historyStatus.currentIndex+1;
    }
    if (historyStatus.currentIndex<0 || historyStatus.currentIndex>commandHistory.length-1) {
      if (historyStatus.currentIndex<0) {
        historyStatus.currentIndex = 0
        return;
      }
      historyStatus.currentIndex = commandHistory.length-1;
      return;
    }
    $self.val(commandHistory[historyStatus.currentIndex]);
  });

  $("#dm_command_wrap form").ajaxForm({
    beforeSubmit: function(data)
    {
      if ($.trim($("#dm_command").val()) == "") return false;
      if (historyStatus.hasBrowsed) {
        commandHistory[commandHistory.length-1] = $("#dm_command").val();
        historyStatus.hasBrowsed = false;
      } else {
        commandHistory.push($("#dm_command").val());
      }
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
      //  future colouring planned (symfony --color)  $lines.append(data.split('[').join('asdasd'));
      $lines.append(data);
      scroll();
      $("#dm_command").val("");
      historyStatus.currentIndex = commandHistory.length-1;
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
