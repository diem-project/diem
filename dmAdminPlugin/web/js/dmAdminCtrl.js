(function($)
{

  $.dm.ctrl = $.extend($.dm.coreEditCtrl, {
  
    init: function()
    {
      this.$ = $("#dm_admin_content");
      
      this.launchControllers(this.$);

      this.fullHeight();
      
      this.bars();
      
      this.flashMessages();
      
      this.datePickers();
      
      if ($form = $('div.sf_admin_form', this.$).orNot()) 
      {
        $form.dmAdminForm(this.options);
      }
      else 
        if ($("body").hasClass('list')) 
        {
          this.listPage();
        }
      
      this.liveEvents();
      
      //    this.breadCrumb();
      
      //    this.filters();
      
      this.search();
    },
    
    //  filters: function()
    //  {
    //    self = this;
    //    $('a.dm_filters_toggler', self.$).click(function() {
    //    }).find(':first').trigger('click');
    //  },
    
    search: function()
    {
      self = this;
      
      $('input.dm_module_search_input', self.$).each(function()
      {
				var $input = $(this).bindKey('return', function()
        {
					var query = $input.val() == $input.attr('title') ? '' : $input.val();
          location.href = $('#sf_admin_container').metadata().baseUrl + '?search=' + query;
          return false;
        })
				.bind('keyup mouseup', function() {
					$('input.dm_module_search_input', self.$).not($input).val($input.val());
				})
				.attr('disabled', false);
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
      if ($fullHeight = $('.full_height').orNot()) 
      {
        $(window).bind("resize", function()
        {
          $fullHeight.height($(window).height() - 50);
        }).trigger("resize");
      }
    },
    
    //  breadCrumb: function()
    //  {
    //    $('#breadCrumb span.s16_home_gray').hover(function() {
    //      $(this).addClass('s16_home_blue').removeClass('s16_home_gray');
    //    },
    //    function() {
    //      $(this).addClass('s16_home_gray').removeClass('s16_home_blue');
    //    });
    //  },
    
    listPage: function()
    {
      self = this;
      
      if ($batchCheckbox = $('#sf_admin_list_batch_checkbox').orNot()) 
      {
        $batchCheckbox.click(function()
        {
          $('input.sf_admin_batch_checkbox', this.$).attr('checked', $batchCheckbox.attr('checked'));
        });
      }
      
      $('div.max_per_page select', self.$).each(function()
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
    }
    
  });
  
})(jQuery);