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
      
      //    this.filters();
    },
    
    //  filters: function()
    //  {
    //    self = this;
    //    $('a.dm_filters_toggler', self.$).click(function() {
    //    }).find(':first').trigger('click');
    //  },
    
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
			
			if ($searchInput = $('#dm_module_search_input').orNot())
			{
				$searchInput.focus();
			}
      
      $('input.sf_admin_list_batch_checkbox', self.$).each(function() {
				$(this).click(function()
        {
          $('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox', self.$).attr('checked', $(this).attr('checked'));
        });
      });
      
      $('input.sf_admin_batch_checkbox, input.sf_admin_list_batch_checkbox', self.$).change(function() {
        $('div.sf_admin_actions > input', self.$).attr('disabled', !$('input.sf_admin_batch_checkbox:checked', self.$).length);
      });
      
      if ($maxPerPage = $('#dm_max_per_page').orNot())
      {
        $maxPerPage.change(function()
        {
          location.href = self.getHref('+/dmAdminGenerator/changeMaxPerPage') + "?dm_module=" + self.options.module + "&max_per_page=" + $maxPerPage.val()
        });
      }
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