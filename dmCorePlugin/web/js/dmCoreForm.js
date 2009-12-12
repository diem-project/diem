(function($)
{
  $.dm.coreForm = {
  
    linkDroppable: function()
    {
      var form = this;
			
      $('input.dm_link_droppable, .dm_link_droppable input', form.element).each(function()
      {
        var $input = $(this);
				
				if (!$input.hasClass('ui-droppable')) 
				{
					if( $input.hasClass('page_only'))
					{
						accept = '#dm_page_bar li';
					}
					else if( $input.hasClass('media_only'))
          {
            accept = '#dm_media_bar li';
          }
					else
					{
						accept = '#dm_page_bar li, #dm_media_bar li.file';
					}
					$input.droppable({
						accept: accept,
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
				}
      });
    }
    
  };
  
})(jQuery);
