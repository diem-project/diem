$.fn.extend({

  dmWidgetNavigationMenuForm: function(widget)
  {
    var self = this,

    formName = self.metadata().form_name,

    $form = self.find('form:first'),

    $tabs = $form.find('div.dm_tabbed_form').dmCoreTabForm(),

    $items = $form.find('.items_list'),
    itemIndex = 0,

    metadata = $items.metadata(),

    createItemElement = function(item)
    {
      item = $.extend({
        position: 0,
        link: '',
        text: '',
        secure: false,
        nofollow: false
      }, item);

      var $li = $('<li class="item_element">')
      .html('\
<a class="item_text s16 s16_layer" title="'+metadata.click_message+'">'+item.text+'</a> \
<ul class="item_form"> \
<li class="clearfix"><label>'+metadata.text_message+':</label><input class="text" type="text" name="'+formName+'[text]['+itemIndex+']" /></li> \
<li class="clearfix"><label>'+metadata.link_message+':</label><input class="link" type="text" name="'+formName+'[link]['+itemIndex+']" /></li> \
<li class="clearfix" style="padding-top: 5px;"> \
<a class="remove s16 s16_delete" style="color: red;float: right">'+metadata.delete_message+'</a> \
<a class="extend s16 s16_add_little" style="color: #559955">'+metadata.extended_show_message+'</a> \
<div class="extended"> \
<ul> \
<li class="clearfix"><label>'+metadata.secure_message+':</label><input class="secure" type="checkbox" name="'+formName+'[secure]['+itemIndex+']" value="1" /></li> \
<li class="clearfix"><label>'+metadata.nofollow_message+':</label><input class="nofollow" type="checkbox" name="'+formName+'[nofollow]['+itemIndex+']" value="1" /></li> \
<li class="clearfix for_depth"><label>'+metadata.depth_message+':</label><select class="depth" name="'+formName+'[depth]['+itemIndex+']">'+self.getDepthOptions(item.depth)+'</select></li>\
</ul> \
</div> \
</li> \
</ul>'
        );

      $li.find('div.extended').hide().end()
        .find('input.text').val(item.text).end()
        .find('input.link').val(item.link).end()
        .find('input.secure').prop('checked', item.secure).end()
        .find('input.nofollow').prop('checked', item.nofollow);
      
      $items.append($li);
      itemIndex++;

      var $itemText = $li.find('a.item_text');

      $itemText.click(function()
      {
        if (!$li.hasClass('dm_dragging'))
        {
          $li.find('ul.item_form').toggle(200);
        }
      })
      .end()
      .find('a.extend').click(function() {
         var extended = $('.extended', $(this).parent());
         $(this).text(extended.is(":hidden") ? metadata.extended_hide_message:metadata.extended_show_message);
         extended.toggle();
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
      .end()
      .find('input.link').bind('keyup', function() {
        $li.find('li.for_depth')[$(this).val().substr(0, 5) == 'page:' ? 'show' : 'hide']();
      }).trigger('keyup');

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
      accept:       '#dm_page_bar li > a',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui)
      {
        createItemElement({
          link: 'page:'+ui.draggable.attr('data-page-id'),
          text: ui.draggable.text()
        });

        $items.attr('scrollTop', 999999);
      }
    }).sortable({
      opacity:                0.5,
      distance:               5,
      revert:                 false,
      scroll:                 true,
      tolerance:              'pointer',
      start:                  function(e, ui) {
        ui.item.addClass('dm_dragging');
      },
      stop:                   function(e, ui) {
        setTimeout(function() {
          ui.item.removeClass('dm_dragging');
        }, 200);
        $(this).trigger('resort');
      }
    });

    $form.find('a.external_link').click(function() {
      createItemElement({
        link: '',
        text: ''
      });

      $items.attr('scrollTop', 999999).find('li.item_element:last a.item_text').trigger('click');
    });
  },

  // show/hide for_depth

  getDepthOptions: function(value)
  {
    value = value || 0;
    html = '';
    for(i=0; i<10; i++)
    {
      text = 0 == i ? '-' : i;
      html += (value == i
        ? '<option value="'+i+'" selected="selected">'+text+'</option>'
        : '<option value="'+i+'">'+text+'</option>');
    }
    return html;
  }
});
