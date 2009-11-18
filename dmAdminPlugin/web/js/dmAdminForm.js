(function($)
{

  $.widget('ui.dmAdminForm', $.extend({}, $.dm.coreForm, {
  
    _init: function()
    {
      this.$ = $("#dm_admin_content");
      
      this.focusFirstInput();
      this.markitup();
      this.selectObject();
      this.checkBoxList();
      this.linkDroppable();
      this.hotKeys();
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
        this.$.bindKey('Ctrl+s', function()
        {
          $save.trigger('click');
          return false;
        });
      }
    },
    
    markitup: function()
    {
      var form = this;
      
      $('textarea.dm_markdown', form.element).each(function()
      {
        $editor = $(this);
        $preview = $editor.closest('div.fieldset_content_inner').find('div.markdown_preview');
        $editor.markItUp(dmMarkitupMarkdown);
        var value = $editor.val();
        setInterval(function()
        {
          if ($editor.val() != value) 
          {
            value = $editor.val();
            $.ajax({
              type: "POST",
              mode: "abort",
              url: $.dm.ctrl.getHref('+/dmCore/markdown'),
              data: {
                text: value
              },
              success: function(html)
              {
                $preview.html(html);
              }
            });
          }
        }, 200);
        
        $preview.height($editor.closest('div.markItUpContainer').innerHeight() - 13);
        
        $editor.resizable({
          alsoResize: $preview,
          handles: 's'
        });
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
    
  }));
  
})(jQuery);
