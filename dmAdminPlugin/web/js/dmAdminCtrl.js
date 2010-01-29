(function($)
{

  $.dm.ctrl = $.extend($.dm.coreEditCtrl, {
  
    init: function()
    {
      this.$ = $("#dm_admin_content");
      
      this.fullHeight();
      
      this.bars();
      
      this.flashMessages();
      
      this.datePickers();
      
      if ($form = $('div.sf_admin_form', this.$).orNot()) 
      {
        $form.dmAdminForm(this.options);
      }
      else if ($("body").hasClass('list')) 
      {
        this.listPage();
      }
      
      this.liveEvents();
			
			this.autoLoading();

      this.checkVersion();

      if($.dm.ping && this.options.authenticated)
      {
        this.launchPing();
      }
    },

    checkVersion: function()
    {
      if($versionCheck = $('#dm_async_version_check').orNot())
      {
        $.ajax({
          url:      $.dm.ctrl.getHref('+/dmAdmin/versionCheck'),
          success:  function(html)
          {
            $versionCheck.html(html).click(function() { $versionCheck.remove(); });
          }
        });
      }
    },
		
		autoLoading: function()
		{
			var self = this, nbElements = 1;
			
			$('.dm_auto_loading', self.$).each(function()
			{
				var $this = $(this), metadata = $this.metadata();
				$this.height(metadata.height || ($this.width()/500) * 300);
        setTimeout(function() {
					$.ajax({
						url:     metadata.url,
						success: function(html) {
							$this.hide().html(html).fadeIn(1000);
						}
					});
			  }, nbElements*500);
				nbElements ++;
			});
		},
    
    bars: function()
    {
      $('#dm_page_bar').dmAdminPageBar();
      
      if ($mediaBar = $('#dm_media_bar').orNot()) 
      {
        $mediaBar.dmAdminMediaBar();
      }
      
      $('#dm_tool_bar').dmAdminToolBar();
    },
    
    fullHeight: function()
    {
      if ($fullHeight = $('div.full_height').orNot()) 
      {
        $(window).bind("resize", function()
        {
          $fullHeight.height($(window).height() - 50);
        }).trigger("resize");
      }
    },
    
    listPage: function()
    {
      var self = this;
      
      if ($searchInput = $('#dm_module_search_input').orNot()) 
      {
        $searchInput.focus();
      }
      
      $('input.sf_admin_list_batch_checkbox', self.$).each(function()
      {
        $(this).click(function()
        {
          $('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox', self.$).attr('checked', $(this).attr('checked'));
        });
      });
      
      $('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox', self.$).change(function()
      {
        $('div.sf_admin_actions > input', self.$).attr('disabled', !$('input.sf_admin_batch_checkbox:checked', self.$).length);
      });
      
      $('select.dm_max_per_page', self.$).each(function()
      {
        $(this).change(function()
        {
          location.href = self.getHref('+/dmAdminGenerator/changeMaxPerPage') + "?dm_module=" + self.options.module + "&max_per_page=" + $(this).val()
        });
      });
    },
    
    datePickers: function()
    {
      if ($.fn.datepicker) 
      {
        $("input.datepicker_me", this.$).datepicker({});
      }
    },
    
    flashMessages: function()
    {
      $("#flash").click(function()
      {
        $(this).remove();
      });
    }
    
  });
  
})(jQuery);
