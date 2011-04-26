(function($) {
  
$.dm.coreToolBar = {
  
  initToolBar: function()
  {
    this.initSelectCulture();

    $('#dm_select_theme').bind('change', function()
    {
      $('body').block();
      location.href = $.dm.ctrl.getHref('+/dmFront/selectTheme')+'?theme='+$(this).val()
    });

    if ($link = $('a.dm_refresh_link', this.element).orNot())
    {
     this.initRefresh($link);
    }
  },
 
  initSelectCulture: function()
  {
    $('#dm_select_culture').bind('change', function()
    {
      $('body').block();
      location.href = $.dm.ctrl.getHref('+/dmCore/selectCulture')+'?culture='+$(this).val()
    });
  },

  initRefresh: function($link)
  {
    var self = this;

    $link.click(function()
    {
      $link.addClass('s16_gear');
      $.blockUI();
      $.ajax({
        url:      $link.attr('href'),
        success:  function(html)
        {
          $.blockUI({
            message: html
          });
          self.refreshStep($('div.blockMsg div.dm_refresh_show').metadata(), 0);
        }
      });
     return false;
    });
  },

  refreshStep: function(data, step)
  {
    var self = this;

    $('div.blockMsg div.dm_refresh_show ul.dm_steps li.current').removeClass('current');

    $('div.blockMsg div.dm_refresh_show ul.dm_steps').append($('<li>').addClass('current').text(data.msg));

    if ('ajax' == data.type)
    {
      $.ajax({
        url:      data.url,
        dataType: 'json',
        success:  function(data)
        {
          self.refreshStep(data, step+1);
        }
      });
    }
    else
    {
     location.href = data.url;
    }
  }

};

})(jQuery);
