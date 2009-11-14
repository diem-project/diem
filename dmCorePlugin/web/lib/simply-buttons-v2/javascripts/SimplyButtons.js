//  Simply Buttons, version 2.0
//  (c) 2007-2009 Kevin Miller
//
//  This script is freely distributable under the terms of an MIT-style license.
// 
/*-----------------------------------------------------------------------------------------------*/
// 
// * Adjusts the buttons so that they will not have an outline when they are pressed.
// * If the browser is mobile then we replace the buttons with inputs for compatibility.
// * Disables the text in the buttons from being selected.
// * The default styles here are meant for use with the Sliding Doors technique http://alistapart.com/articles/slidingdoors/
//     to be used for IE so we can have nice states with a horrid browser too!
//
/*-----------------------------------------------------------------------------------------------*/

var SimplyButtons = {
  
  options : {
    hyperlinkClass : 'button'
    ,activeButtonClass : 'button_active'
    ,states : {
      outer : {
        active : {
          backgroundPosition : 'bottom left'
        }
        ,inactive : {
          backgroundPosition : 'top left'
        }
      }
      ,inner : {
        active : {
          backgroundPosition : 'bottom right'
        }
        ,inactive : {
          backgroundPosition : 'top right'
        }
      }
    }
    ,iphone : {
      replaceButtons : true
    }
  }
  
  ,buttons : []
  
  ,iphone : false
  
  ,init : function(options)
  {
    for (var property in options)
    {
      this.options[property] = options[property];
    }
    
    this.iphone = (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i));
    
    this.process(document.getElementsByTagName('button'), false);
    this.process(document.getElementsByTagName('a'), true);
    
    if (this.iphone && this.options.iphone.replaceButtons)
    {
      this.remove();
    }
  }

  ,process : function(elements, links)
  {
    var linkTest = new RegExp('\\b' + this.options.hyperlinkClass + '\\b');
    for (var a = 0; a < elements.length; a++)
    {
      if ((links && linkTest.test(elements[a].className)) || !links)
      {
        if (this.iphone && !links)
        {
          this.mobile(elements[a]);
        }
        else
        {
          this.disable(elements[a]);
          this.setup(elements[a]);
        }
        
        if (!links)
        {
          this.buttons.push(elements[a]);
        }
      }
    }
  }
  
  ,mobile : function(element)
  {
		var input = document.createElement('input');
		input.setAttribute('type', element.getAttribute('type') == 'submit' ? 'submit' : 'button');
    
		var attributes = new Array('id', 'name', 'value', 'class', 'onclick', 'onmouseover', 'onmouseout', 'onpress', 'onfocus', 'onblur', 'onmouseup', 'onmousedown');
		for (var a = 0; a < attributes.length; a++)
		{
			if (element.getAttribute(attributes[a]))
			{
				input.setAttribute(attributes[a], element.getAttribute(attributes[a])); 			
			}
		}
		
		input.style.marginLeft = element.style.marginLeft;
		input.style.marginRight = element.style.marginRight;

		element.parentNode.insertBefore(input, element);
		
	}
	
	,remove : function()
	{
	  for (var a = 0; a < this.buttons.length; a++)
    {
	    this.buttons[a].parentNode.removeChild(this.buttons[a]);
    }      
  }
   
  ,disable : function(element)
  {
    element.onselectstart = function() { return false; };
    element.style.MozUserSelect = 'none';
    element.style.KhtmlUserSelect = 'none';
    element.style.UserSelect = 'none';
    element.style.cursor = 'default';
  }
  
  ,setup : function(element) 
  {
    if (document.all)
    {
      if (element.tagName == 'BUTTON')
      {
        element.attachEvent('onfocus', this.bind(this.toggle, this, element));
      }
      else
      {
        element.attachEvent('onmousedown', this.bind(this.toggle, this, element));
      }
      element.attachEvent('onmouseup', this.bind(this.toggle, this, element));
    }
    else
    {
      element.onfocus = function() { this.blur(); };
    }
  }
  
  ,toggle : function(o, element)
  {
    if (element.tagName != 'BUTTON' && element.tagName != 'A')
    {
      while (element.tagName != 'A')
      {
        element = element.parentNode;
      }
    }
    if (event.type == 'focus' || event.type == 'mousedown')
    {
      element.className += ' ' + o.options.activeButtonClass;
      o.style(element.childNodes[0], o.options.states.inner.active);
      o.style(element.childNodes[0].childNodes[0], o.options.states.outer.active);
      element.blur();
    } 
    else
    {
      element.className = element.className.replace(o.options.activeButtonClass, '');
      o.style(element.childNodes[0], o.options.states.inner.inactive);
      o.style(element.childNodes[0].childNodes[0], o.options.states.outer.inactive);
    }
  }
  
  ,style : function(element, styles)
  {
    for (var property in styles)
    {
      element.style[property] = styles[property];
    }    
  }
  
  ,bind : function(func)
  {
    var args = [];
    for (var a = 1; a < arguments.length; a++)
    {
      args.push(arguments[a]);
    }
    return function() { return func.apply(this, args); };
  }

};