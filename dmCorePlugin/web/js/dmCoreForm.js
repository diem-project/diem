(function($)
{
  $.dm.coreForm = {
  
    linkDroppable: function()
    {
      var form = this;
			
      $('input.dm_link_droppable', form.element).each(function()
      {
        var $input = $(this).droppable({
          accept: '#dm_page_bar li, #dm_media_bar li.file',
          activeClass: 'droppable_active',
          hoverClass: 'droppable_hover',
          //          tolerance:    'touch',
          drop: function(e, ui)
          {
            if (ui.draggable.hasClass('file')) 
            {
              $input.val('media:' + ui.draggable.attr('id').replace(/dmm/, '') + ' ' + ui.draggable.find('span.name:first').text().replace(/\s/g, ''));
            }
            else 
            {
              $input.val('page:' + ui.draggable.attr('id').replace(/dmp/, '') + ' ' + ui.draggable.find('>a').text());
            }
          }
        });
      });
      
      $('textarea.dm_markdown', form.element).each(function()
      {
        var $elem = $(this).droppable({
          accept: '#dm_page_bar li',
          activeClass:  'droppable_active',
          hoverClass:   'droppable_hover',
          //          tolerance:    'touch',
          drop: function(e, ui)
          {
						var selection = $elem.getSelection().text,
						linkText = selection || ui.draggable.find('>a').text(),
						type = "page",
						placeholder = "["+linkText+"]("+type+":"+ui.draggable.attr('id').replace(/dmp/, '')+")",
						scrollTop = $elem.scrollTop();
		        
						if (selection) 
						{
							$elem.replaceSelection(placeholder, true);
						}
						else
						{
							$elem.val($elem.val()+' '+placeholder);
						}
						
		        $elem.scrollTop(scrollTop);
          }
        });
      });
    }
    
  };
  
})(jQuery);
