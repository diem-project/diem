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
          //          tolerance:    'touch',
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
      if ($firstInput = $('div.sf_admin_form_row_inner :input:first', this.$))
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

        $container.find('div.markItUpHeader > ul').append(
          $('<li class="markitup_full_screen"><a title="Enlarge the editor">+</a></li>')
          .unbind('click.dm').bind('click.dm', function() {
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

        $lis.find('> label, > input').unbind('click.dm').bind('click.dm', function(e)
        {
          e.stopPropagation();
        });

        $lis.unbind('click.dm').bind('click.dm', function()
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
          $(this).unbind('click.dm').bind('click.dm', function()
          {
            $(this).closest('div.sf_admin_form_row_inner').find('input:checkbox:visible').attr('checked', $(this).hasClass('select_all')).trigger('change');
          });
        });

        if($lis.length > 9)
        {
          $('<div class="dm_checkbox_search"><input type="text" title="Search" /></div>')
          .prependTo($list.parent())
          .find('input').bind('keyup', function()
          {
            var term = $.trim($(this).val());

            if(term == '')
            {
              $lis.show();
              return;
            }

            $lis.each(function()
            {
              $(this)[$(this).find('label').text().toLowerCase().indexOf(term.toLowerCase()) != -1 ? 'show' : 'hide']();
            });
          }).tipsy({gravity: $.fn.tipsy.autoSouth});
        }
      });
    }
    
  });
  
})(jQuery);