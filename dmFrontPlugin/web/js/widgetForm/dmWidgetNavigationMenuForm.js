$.fn.extend({

  dmWidgetNavigationMenuForm: function(widget)
  {
    var self = this,

		formName = self.metadata().form_name,

		$form = self.find('form:first'),

		$tabs = $form.find('div.dm_tabbed_form').dmCoreTabForm(),

		$items = $form.find('.items_list'),

	  deleteMessage = $items.metadata().delete_message,

    createItemElement = function(item)
    {
			item = $.extend({
				position: 0,
				link: '',
				text: ''
			}, item);

			var $li = $('<li class="item_element">')
      .html(' \
<input class="id" type="hidden" name="'+formName+'[link][]" value="'+item.link+'" /> \
<input class="position" type="hidden" name="'+formName+'[item_position][]" value="'+item.position+'" /> \
<div class="item_text">'+item.text+'</div> \
<img src="'+$.dm.ctrl.options.dm_core_asset_root+'images/cross-small.png" class="delete_item_element" title="'+deleteMessage+'" />'
      )
      .block();

      $items.append($li);

      self.dmFrontForm('linkDroppable');
      
      if ($items.hasClass('ui-sortable'))
      {
        $items.sortable('refresh').trigger('resort');
      }
    };

		$.each($items.metadata().items, function() {
			createMediaElement(this);
		});

		$items.droppable({
      accept:       '#dm_page_bar li',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui)
			{
				createItemElement({link: 'page:'+ui.draggable.attr('id').replace(/dmp/, '')});

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
      }
		}).bind('resort', function() {
      $('li.item_element', $items).each(function(index) {
        $('input.position', $(this)).val(index);
      });
		});
  }
});