$.fn.extend({

  dmWidgetNavigationMenuForm: function(widget)
  {
    var self = this,

		formName = self.metadata().form_name,

		$form = self.find('form:first'),

		$tabs = $form.find('div.dm_tabbed_form').dmCoreTabForm(),

		$items = $form.find('.items_list'),

    metadata = $items.metadata(),

    createItemElement = function(item)
    {
			item = $.extend({
				position: 0,
				link: '',
				text: ''
			}, item);

			var $li = $('<li class="item_element">')
      .html('\
<a class="item_text" title="'+metadata.click_message+'">'+item.text+'</a> \
<ul class="item_form"> \
<li class="clearfix"><label>'+metadata.text_message+':</label><input class="text" type="text" name="'+formName+'[text][]" value="'+item.text+'" /></li> \
<li class="clearfix"><label>'+metadata.link_message+':</label><input class="link" type="text" name="'+formName+'[link][]" value="'+item.link+'" /></li> \
<li class="clearfix"><a class="remove">'+metadata.delete_message+' '+item.text+'</li> \
</ul>'
      );

      $items.append($li);

      var $itemText = $li.find('a.item_text');

      $itemText.click(function()
      {
        if (!$li.hasClass('dm_dragging'))
        {
          $li.find('ul.item_form').toggle(200);
        }
      })
      .end()
      .find('a.remove').click(function() {
        if (confirm($(this).text()+' ?'))
        {
          $li.remove();
        }
      })
      .end()
      .find('input.text').bind('keyup', function() {
        $itemText.text($(this).val());
      })
      ;

      self.dmFrontForm('linkDroppable');
      
      if ($items.hasClass('ui-sortable'))
      {
        $items.sortable('refresh');
      }
    };

		$.each($items.metadata().items, function() {
			createItemElement(this);
		});

		$items.droppable({
      accept:       '#dm_page_bar li',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui)
			{
        createItemElement({
          link: 'page:'+ui.draggable.attr('id').replace(/dmp/, ''),
          text: ui.draggable.find('>a').text()
        });

				$items.attr('scrollTop', 999999);
      }
    }).sortable({
      opacity:                0.5,
      distance:               5,
      revert:                 false,
      scroll:                 true,
      tolerance:              'pointer',
      stop:                   function(e, ui) {
				$(this).trigger('resort');
      },
      start:                  function(e, ui) {
        ui.item.addClass('dm_dragging');
      },
      stop:                   function(e, ui) {
        setTimeout(function() { ui.item.removeClass('dm_dragging'); }, 200);
      }
		});
  }
});