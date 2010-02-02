(function($)
{

  $.widget('ui.dmAdminForm', {
  
    _init: function()
    {
      this.$ = $("#dm_admin_content");
      
      this.focusFirstInput();
      this.markdown();
      this.selectObject();
      this.checkBoxList();
      this.droppableInput();
      this.hotKeys();
    },

    droppableInput: function()
    {
      $('input.dm_link_droppable, .dm_link_droppable input', this.element).dmDroppableInput();
    },
    
    focusFirstInput: function()
    {
      if ($firstInput = $('div.sf_admin_form_row_inner input:first', this.$)) 
      {
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

        var resize = function()
        {
          $preview.height($container.innerHeight() - 13);

          $editor.resizable({
            alsoResize: $preview,
            handles: 's'
          }).width($container.width()-6);
        };

        $container.find('div.markItUpHeader ul').append(
          $('<li class="markitup_full_screen"><a title="Full Screen">Full Screen</a></li>')
          .click(function() {
            $container.toggleClass('dm_markdown_full_screen');

            if($container.hasClass('dm_markdown_full_screen'))
            {
              $editor
              .data('old_height', $editor.height())
              .resizable('destroy').height($(window).height()-90);
              resize();
              window.scrollTo(0, Math.round($container.offset().top) - 40);
            }
            else
            {
              $editor.resizable('destroy').height($editor.data('old_height'));
              resize();
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

        resize();
      });
    },
    
    selectObject: function()
    {
      // Switch to another object
      $("#dm_select_object").bind('change', function()
      {
        location.href = $(this).metadata().href.replace('_ID_', $(this).val());
      });
    },
    
    checkBoxList: function()
    {
      var $list = $('ul.checkbox_list', this.element);
      
      $('> li > label, > li > input', $list).click(function(e)
      {
        e.stopPropagation();
      });
      
      $('> li', $list).click(function()
      {
        var $input = $('> input', $(this));
        $input.attr('checked', !$input.attr('checked')).trigger('change');
      });
      
      $('> li > input', $list).change(function()
      {
        $(this).parent()[($(this).attr('checked') ? 'add' : 'remove') + 'Class']('active');
        return true;
      }).trigger('change');
      
      $('div.control span.select_all, div.control span.unselect_all', $list.parent().parent()).each(function()
      {
        $(this).click(function()
        {
          $(this).closest('div.sf_admin_form_row_inner').find('input:checkbox').attr('checked', $(this).hasClass('select_all')).trigger('change');
        });
      });
    }
    
  });
  
})(jQuery);
