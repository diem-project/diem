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
      else if ($("#dm_admin_content").hasClass('action_index'))
      {
        this.listPage();
      }
      
      this.liveEvents();
			
			this.autoLoading();

      this.showMoreRelatedRecords();

      this.checkVersion();

      if($.dm.ping && this.options.authenticated)
      {
        $.dm.ping.init(this.options);
      }

      this.tips();
    },

    tips: function()
    {
      this.$.find('a[title]:not(div.dm_form_action_bar_bottom a), input[title]').tipsy({gravity: $.fn.tipsy.autoNorth});

      this.$.find('div.dm_form_action_bar_bottom a[title]').tipsy({gravity: $.fn.tipsy.autoSouth});
    },

    showMoreRelatedRecords: function()
    {
      this.$.find('a.show_more_related_records').one('click', function()
      {
        $(this).parent().parent().block().load($(this).attr('href'));
        return false;
      });
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

      self.$.find('input.sf_admin_list_batch_checkbox').each(function()
      {
        $(this).click(function()
        {
          self.$.find('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox').attr('checked', $(this).attr('checked'));
        });
      });
      
      self.$.find('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox').change(function()
      {
        // google chrome requires setTimeout
        setTimeout(function()
        {
          self.$.find('div.sf_admin_actions > input').attr('disabled', !self.$.find('input.sf_admin_batch_checkbox:checked').length);
        }, 50);
      });
      
      self.$.find('select.dm_max_per_page').each(function()
      {
        $(this).change(function()
        {
          self.$.block();
          location.href = self.getHref('+/dmAdminGenerator/changeMaxPerPage') + "?dm_module=" + self.options.module + "&max_per_page=" + $(this).val()
        });
      });

      // toggle booleans
      self.$.find('td.sf_admin_boolean a').click(function() {
        $(this).toggleClass('s16_tick s16_cross');
        $.ajax({
          url:      self.$.find('tbody').metadata().toggle_url,
          data:     {
            field:  $(this).metadata().field,
            pk:     $(this).parent().parent().metadata().pk
          },
          success:  function(data) {
            $(this).toggleClass('s16_tick', '1' == data).toggleClass('s16_cross', '0' == data);
          }
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
