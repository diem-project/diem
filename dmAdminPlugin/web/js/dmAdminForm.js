(function($) {
  
$.widget('ui.dmAdminForm', $.extend({}, $.dm.coreForm, {

  _init : function()
	{
    this.$ = $("#dm_admin_content");
    
		this.markitup();
    this.selectObject();
    this.checkBoxList();
		this.linkDroppable();
		this.hotKeys();
  },
	
	hotKeys: function()
	{
		if ($save = $('div.sf_admin_action_save:first input', this.$).orNot())
		{
			this.$.bindKey('Ctrl+s', function() { $save.trigger('click'); return false;});
		}
	},
	
  markitup: function()
  {
    var form = this;
    
    $('textarea.markdown_editor', form.element).each(function() {
      $editor = $(this);
      $preview = $editor.closest('div.fieldset_content_inner').find('div.markdown_preview');
      $editor.markItUp(dmMarkitupMarkdown);
      var value = $editor.val();
      setInterval(function() {
        if ($editor.val() != value)
        {
          value = $editor.val();
          $.ajax({
            type:    "POST",
            mode:    "abort",
            url:     form.options.relative_url_root+"dm_light.php",
            data:    { action: 'markdown', text: value },
            success: function(html) {
              $preview.html(html);
            }
          });
        }
      }, 200);
      
      $preview.height($editor.closest('div.markItUpContainer').innerHeight()-13);
      
      $editor.resizable({
        alsoResize: $preview,
        handles: 's'
      });
    });
  },

  selectObject: function()
  {
    // Switch to another object
    $("#dm_select_object").bind('change', function() {
      location.href = $(this).metadata().href.replace('_ID_', $(this).val());
    });
  },
  
//  doubleList: function()
//  {
//    // Double lists
//    if ($doubleList = $("div.dm_double_list", this.element).orNot()) {
//      $selects = $('select', $doubleList);
//      sfDoubleList.init($selects[0], $selects[1].className);
//    }
//  },
  
  checkBoxList: function()
  {
    var form = this;
    
    $('ul.checkbox_list > li', form.element).click(function() {
      var $input = $('> input', $(this));
      $input.attr('checked', !$input.attr('checked')).trigger('change');
    });
    
    $('ul.checkbox_list > li > input', form.element).change(function() {
      $(this).parent()[($(this).attr('checked') ? 'add' : 'remove')+'Class']('active');
			return true;
    }).trigger('change');
  }
  
}));

})(jQuery);