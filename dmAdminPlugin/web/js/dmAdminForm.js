(function($)
{

  $.widget('ui.dmAdminForm', {
  
    _init: function()
    {
      this.$ = $("#dm_admin_content");
      
      this.focusFirstInput();
      this.markdown();
      this.checkBoxList();
      this.droppableInput();
      this.droppableMedia();
      this.hotKeys();
      this.rowColors();
      this.pagination();
    },

    rowColors: function()
    {
      var self = this;

      self.element.find('div.sf_admin_form_row').each(function()
      {
        var $row = $(this);
        $row.find('input, textarea, select').each(function()
        {
          var initialValue = $(this).val()+$(this).attr('checked');
          var event = $(this).is('input, textarea') ? 'change keyup click' : 'change';
          $(this).bind(event, function()
          {
            $row.toggleClass('dm_row_modified', $(this).val()+$(this).attr('checked') != initialValue);
          });
        });
      });
    },

    droppableMedia: function()
    {
      var self = this;
      
      self.element.find('ul.dm_media_for_record_form').each(function()
      {
        var $this = $(this);
        var fieldName = $this.closest('div.sf_admin_form_row').attr('data-field-name');
        var viewClass = 'sf_admin_form_field_'+fieldName.replace(/_form/, '_view');
        
        $(this).droppable({
          accept: '#dm_media_bar li',
          activeClass: 'droppable_active',
          hoverClass: 'droppable_hover',
          // tolerance: 'touch',
          drop: function(e, ui)
          {
            var mediaId = ui.draggable.attr('id').replace(/dmm/, '');
            $this.find('input.dm_media_id').val(mediaId);

            if($view = self.element.find('div.'+viewClass).orNot())
            {
              $view.block().load($.dm.ctrl.getHref('+/dmMedia/preview?id='+mediaId)).unblock();
            }
          }
        });
      });
    },

    droppableInput: function()
    {
      $('input.dm_link_droppable, .dm_link_droppable input', this.element).dmDroppableInput();
    },
    
    focusFirstInput: function()
    {
      if(this.alreadyDone) return;
      if ($firstInput = $('div.sf_admin_form_row_inner input:first', this.$)) 
      {
        this.alreadyDone = true;
        $firstInput.focus();
      }
    },
    
    hotKeys: function()
    {
      if ($save = $('li.sf_admin_action_save:first input', this.$).orNot()) 
      {
        var self = this;

        setTimeout(function()
        {
          self.$.bindKey('Ctrl+s', function()
          {
            $save.trigger('click');
            return false;
          });
        }, 1000);
      }
    },
    
    markdown: function()
    {
      var form = this;
      
      $('textarea.dm_markdown', form.element).each(function()
      {
        var $editor = $(this);
        var $preview = $('#dm_markdown_preview_'+$editor.metadata().code);
        var value = $editor.val();
        
        $editor.dmMarkdown();

        var $container = $editor.closest('div.markItUpContainer');

        $editor.bind('scroll', function()
        {
          if($editor.scrollTop() == 0)
          {
            $preview.scrollTop(0);
          }
          else if($editor.scrollTop()+$editor.height() == $editor[0].scrollHeight)
          {
            $preview.scrollTop($preview[0].scrollHeight - $preview.height());
          }
        });

        $container.find('div.markItUpHeader ul').append(
          $('<li class="markitup_full_screen"><a title="Enlarge the editor">+</a></li>')
          .click(function() {
            $container.toggleClass('dm_markdown_full_screen');

            if($container.hasClass('dm_markdown_full_screen'))
            {
              $editor
              .data('old_height', $editor.height())
              .height($(window).height()-90)
              .parent().height($(window).height()-84);

              $preview.height($container.innerHeight() - 20);
              
              window.scrollTo(0, Math.round($container.offset().top) - 40);
            }
            else
            {
              $editor
              .height($editor.data('old_height'))
              .parent().height($editor.data('old_height')+6);

              $preview.height($container.innerHeight() - 20);
            }
          })
        );
        
        setInterval(function()
        {
          if ($editor.val() != value) 
          {
            value = $editor.val();
            $.ajax({
              type: "POST",
              mode: "abort",
              url: $.dm.ctrl.getHref('+/dmCore/markdown')+"?dm_nolog=1",
              data: {
                text: value
              },
              success: function(html)
              {
                $preview.html(html);
              }
            });
          }
        }, 500);


        $preview.height($container.innerHeight() - 13);

        $editor.resizable({
          alsoResize: $preview,
          handles: 's'
        }).width($container.width()-6);
      });
    },
    
    checkBoxList: function()
    {
      var self = this;
      
      $('ul.checkbox_list', self.element).each(function()
      {
        var $list = $(this), $lis = $('> li', $list);

        $lis.find('> label, > input').click(function(e)
        {
          e.stopPropagation();
        });

        $lis.click(function()
        {
          var $input = $('> input', $(this));
          $input.attr('checked', !$input.attr('checked')).trigger('change');
        });

        $lis.find('> input').change(function()
        {
          $(this).parent()[($(this).attr('checked') ? 'add' : 'remove') + 'Class']('active');
          return true;
        }).trigger('change');

        $('div.control span.select_all, div.control span.unselect_all', $list.parent().parent()).each(function()
        {
          $(this).click(function()
          {
            $(this).closest('div.sf_admin_form_row_inner').find('input:checkbox:visible').attr('checked', $(this).hasClass('select_all')).trigger('change');
          });
        });

        $('.dm_checkbox_search_filter > input, .clear > a', self.element).tipsy({gravity: $.fn.tipsy.autoSouth});
        
      });
      
      $('.see_selected', self.element).unbind('click').click(function(){
    	  
    	  $('ul.checkbox_list > li', $(this).parent().parent().parent().children('.content')).each(function(){
    		  if(!$(this).hasClass('active')){
    			  $(this).fadeOut();
    		  }else{
    			  $(this).fadeIn();
    		  }
    	  });
      });
      
      $('.see_unselected', self.element).unbind('click').click(function(){
    	  
    	  $('ul.checkbox_list > li', $(this).parent().parent().parent().children('.content')).each(function(){
    		  if($(this).hasClass('active')){
    			  $(this).fadeOut();
    		  }else{
    			  $(this).fadeIn();
    		  }
    	  });
      });
      
      $('.see_all', self.element).unbind('click').click(function(){
    	  
    	  $('ul.checkbox_list > li', $(this).parent().parent().parent().children('.content')).each(function(){
    		  $(this).fadeIn();
    	  });
      });
      
      $('.clear > a', self.element).unbind('click').click(function(){
    	  var self = $(this);
    	  $(this).parent().parent().children('input').val('');
    	  var pagination = $(this).parent().parent().parent().find('.dm_form_pagination');
    	  var metadata = pagination.metadata();
    	  self.parent().parent().parent().parent().block();
		  var link = metadata.link;
		  var requestedPage = 1;
		  link += '/page/' + requestedPage;
		  link += '/maxPerPage/' + self.parent().parent().parent().find('.dm_max_per_page').val();
		  
		  $.ajax({
			  url: link,
			  success: function(data){
				  data = $(data).find('.fieldset_content_inner .sf_widget_form_dm_doctrine_choice .content');
				  self.parent().parent().parent().parent().html(data.html()).removeAttr('style');
				  $('.tipsy').fadeOut(function(){$('.tispy').remove()});
				  $.dm.ctrl.init();
				  self.parent().parent().parent().parent().unblock();
			  }
		  });
		  return false;
      });
      
      
    },

	pagination: function(){
		
		$('.dm_checkbox_tools > .dm_form_pagination a').unbind('click').click(function(e){
			  var self = $(this);
			  e.preventDefault();
			  self.parent().parent().parent().block();
			  var target = $(e.originalTarget);
			  var metadata = $(this).parent().metadata();
			  var link = metadata.link;
			  var currentPage = metadata.currentPage;
			  var requestedPage = null;
			  if(target.hasClass('s16_next'))
			  {
				  requestedPage = currentPage + 1;
			  }else if( target.hasClass('s16_previous'))
			  {
				  requestedPage = currentPage - 1;
			  }
			  else if(target.hasClass('s16_first')){
				  requestedPage = 1;
			  }else{
				  requestedPage = metadata.lastPage;
			  }
			  link += '/page/' + requestedPage;
			  link += '/maxPerPage/' + self.parent().parent().parent().find('.dm_max_per_page').val();
			  
			  var search = self.parent().parent().parent().find('.search-box').val();
			  if(search.length > 0){
				  link += '/search/' + search;
			  }
			  $.ajax({
				  url: link,
				  success: function(data){
					  data = $(data).find('.fieldset_content_inner .sf_widget_form_dm_paginated_doctrine_choice .content');
					  self.parent().parent().parent().html(data.html()).removeAttr('style');
					  $('.tipsy').fadeOut(function(){$('.tispy').remove()});
					  $.dm.ctrl.init();
					  self.parent().parent().parent().unblock();
				  }
			  });
			  return false;
		  });
		  
		  $('.dm_max_per_page', self.element).unbind('change').bind('change', function(e){
			  var self = $(this);
			  e.preventDefault();
			  self.parent().parent().parent().block();
			  var target = $(e.originalTarget);
			  var metadata = $(this).parent().metadata();
			  var link = metadata.link;
			  var currentPage = metadata.currentPage;
			  var requestedPage = null;
			  link += '/maxPerPage/' + $(this).val();
			  var search = self.parent().parent().parent().find('.search-box').val();
			  if(search.length > 0){
				  link += '/search/' + search;
			  }
			  $.ajax({
				  url: link,
				  success: function(data){
					  data = $(data).find('.fieldset_content_inner .sf_widget_form_dm_paginated_doctrine_choice .content');
					  self.parent().parent().parent().html(data.html()).removeAttr('style');
					  $('.tipsy').fadeOut(function(){$('.tispy').remove()});
					  $.dm.ctrl.init();
					  self.parent().parent().parent().unblock();
				  }
			  });
		  });
		  
		  $('.search-box', this.element).unbind('keypress').bind('keypress', function(e){
	    		var self = $(this);
	    		if(e.keyCode === 13)
    			{
	    			var searchBox = $(this).parent().children('.search-box');
		    		var searching = searchBox.val();
		    		var metadata = $(this).parent().parent().children('.dm_form_pagination').metadata();
		    		var link = metadata.link;
		    		self.parent().parent().parent().block();
		    		link += '/search/' + searching;
		    		link += '/page/1';
		    		link += '/maxPerPage/' + $(this).parent().parent().find('.dm_max_per_page').val();
		    		$('.tipsy-inner').remove();
		    		$.ajax({
		    			url: link,
		    			success: function(data){
		    				  data = $(data).find('.fieldset_content_inner .sf_widget_form_dm_paginated_doctrine_choice .content');
							  self = self.parent().parent().parent().html(data.html()).removeAttr('style');
							  $('.tipsy').remove();
							  $('.tipsy-inner').remove();
							  $.dm.ctrl.init();
							  self.parent().parent().parent().parent().unblock();
							  self.find('.search-box').focus();
		    			}
		    		});
		    		return false;	    			
    			}
	    		else{
	    			return true;
	    		}
	    	});
	}
    
  });
  
})(jQuery);