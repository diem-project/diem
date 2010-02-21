(function($)
{
  $.dm.loadedJavascripts = new Array();
  $.dm.loadedStylesheets = new Array();

  $.fn.extend({

    dmDroppableInput: function(callback)
    {
      return this.each(function()
      {
        var $input = $(this);

        if (!$input.hasClass('ui-droppable'))
        {
          if($input.hasClass('page_only'))
          {
            accept = '#dm_page_bar li';
          }
          else if($input.hasClass('media_only'))
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

              callback && $.isFunction(callback) && callback.apply($input);
            }
          });
        }
      });
    },
  
    maxLength: function(max)
    {
      var $elem = this.addClass('dm_max_length'), $helper = $('<span class="dm_max_length_helper">').insertAfter($elem);
      
      $helper.bind('change.maxLength', function()
      {
        var freeChars = max - $elem.val().length;
        $helper.text(freeChars)[freeChars > 0 ? 'removeClass' : 'addClass']('max_length_alert');
      }).trigger('change.maxLength');
      
      $elem.bind('keyup click', function()
      {
        $helper.trigger('change.maxLength');
      });
    },
    
    outClick: function(callback)
    {
      callback = callback || null;
      
      if (callback == 'remove') 
      {
        return $(document).unbind('mousedown.outClick');
      }
      var $elem = this;
      $(document).bind('mousedown.outClick', function(e)
      {
        var a = $elem.offset(), b = {
          top: a.top + $elem.outerHeight(),
          left: a.left + $elem.outerWidth()
        };
        if (e.pageY < a.top || e.pageY > b.top || e.pageX < a.left || e.pageX > b.left) 
        {
          $(document).unbind('mousedown.outClick');
          if ($.isFunction(callback)) 
          {
            callback.apply($elem);
          }
          else 
          {
            $elem.hide();
          }
        }
      });
    },
    
    dmAjaxForm: function(options)
    {
      return this.ajaxForm($.extend({
        data: $.dm.defaults.ajaxData
      }, options));
    },

    // Detects javascripts and stylesheet inclusions and append them to the document
    dmExtractEncodedAssets: function()
    {
      if($encodedAssetsDiv = this.find('div.dm_encoded_assets').orNot())
      {
        data = jQuery.parseJSON($encodedAssetsDiv.html());

        $encodedAssetsDiv.remove();

        for (i in data.css)
        {
          if (-1 == $.inArray(data.css[i], $.dm.loadedStylesheets))
          {
            $('head').append('<link rel="stylesheet" href="' + data.css[i] + '" />');
            $.dm.loadedStylesheets.push(data.css[i]);
          }
        }

        for (var i in data.js)
        {
          if (-1 == $.inArray(data.js[i], $.dm.loadedJavascripts))
          {
            ajaxDefaultData = $.ajaxSettings.data;
            $.ajaxSettings.data = null;
            
            $.ajax({
              url:      data.js[i],
              dataType: 'script',
              cache:    !$.dm.ctrl.options.debug,
              async:    false
            });

            $.ajaxSettings.data = ajaxDefaultData;

            $.dm.loadedJavascripts.push(data.js[i]);
          }
        }
      }

      return this;
    }
  });
  
  $.widget('ui.dmMenu', {

    options: {
      hoverClass: 'hover',
      open:       function(){}
    },
  
    _init: function()
    {
      var self = this;
      
      self.$tabs = $('> ul > li', self.element);
      
      $('> a', self.$tabs).click(function()
      {
        if (!self.lock)
        {
          self.open($(this).parent());
        }
      });
    },
    
    open: function($tab)
    {
      var self = this;
      
      $tab.addClass(this.options.hoverClass).find('> ul').outClick(function()
      {
        self.close();
        self.lock = true;
        $(document).bind('mouseup', function()
        {
          setTimeout(function()
          {
            self.lock = false;
          }, 50);
        });
      });
      
      self.$tabs.not($tab).bind('mouseover', function()
      {
        self.close().open($(this));
      });

      self.options.open.apply($tab);
    },
    
    close: function()
    {
      this.lock = false;
      this.$tabs.unbind('mouseover').filter('li.' + this.options.hoverClass).removeClass(this.options.hoverClass).outClick('remove');
      return this;
    }
    
  });

  $.fn.tipsy.remove = function()
  {
    $('body > div.tipsy').remove();
  }
 
 /*
  * Make ui dialogs position: fixed
  */
 $(function()
  {
  if ($.ui.dialog) 
  {
   $.ui.dialog.prototype._position = function(pos)
   {
    var wnd = $(window), pTop = 0, pLeft = 0, minTop = pTop;
    
    if ($.inArray(pos, ['center', 'top', 'right', 'bottom', 'left']) >= 0) 
    {
     pos = [pos == 'right' || pos == 'left' ? pos : 'center', pos == 'top' || pos == 'bottom' ? pos : 'middle'];
    }
    
    if (pos.constructor != Array && pos.constructor != Object) 
    {
     pos = ['center', 'middle'];
    }
    
    if (pos[0].constructor == Number) 
    {
     pLeft += pos[0];
    }
    else 
    {
     pLeft += (wnd.width() - this.uiDialog.outerWidth()) / 2;
    }
    
    if (pos[1].constructor == Number) 
    {
     pTop += pos[1];
    }
    else 
    {
     var dialogHeight = 350;
     pTop += (wnd.height() - dialogHeight) / 2;
    }
    
    // prevent the dialog from being too high (make sure the titlebar is accessible)
    pTop = Math.max(pTop, minTop);
    this.uiDialog.css({
     position: 'fixed',
     top: pTop,
     left: pLeft
    });
   };
  }
  });

})(jQuery);