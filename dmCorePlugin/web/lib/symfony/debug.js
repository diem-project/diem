function sfWebDebugToggleMenu()
{
  var element = document.getElementById('sfWebDebugDetails');

  var cacheElements = $('div.sfWebDebugCache');
  var mainCacheElements = $('div.sfWebDebugActionCache');
  var panelElements = $('#sfWebDebug div.sfWebDebugTop');

  if (element.style.display != 'none')
  {
    for (var i = 0; i < panelElements.length; ++i)
    {
      panelElements[i].style.display = 'none';
    }

    // hide all cache information
    for (var i = 0; i < cacheElements.length; ++i)
    {
      cacheElements[i].style.display = 'none';
    }
    for (var i = 0; i < mainCacheElements.length; ++i)
    {
      mainCacheElements[i].style.border = 'none';
    }
  }
  else
  {
    for (var i = 0; i < cacheElements.length; ++i)
    {
      cacheElements[i].style.display = '';
    }
    for (var i = 0; i < mainCacheElements.length; ++i)
    {
      mainCacheElements[i].style.border = '1px solid #f00';
    }
  }

  sfWebDebugToggle('sfWebDebugDetails');
  sfWebDebugToggle('sfWebDebugShowMenu');
  sfWebDebugToggle('sfWebDebugHideMenu');
}

function sfWebDebugShowDetailsFor(element)
{
  if (typeof element == 'string')
    element = document.getElementById(element);

  var panelElements = $('#sfWebDebug div.sfWebDebugTop');
  for (var i = 0; i < panelElements.length; ++i)
  {
    if (panelElements[i] != element)
    {
      panelElements[i].style.display = 'none';
    }
  }

  sfWebDebugToggle(element);
}

function sfWebDebugToggle(element)
{
  if (typeof element == 'string')
    element = document.getElementById(element);

  if (element)
    element.style.display = element.style.display == 'none' ? '' : 'none';
}

function sfWebDebugToggleMessages(klass)
{
  var elements = $('.'+klass);

  var x = elements.length;
  for (var i = 0; i < x; ++i)
  {
    sfWebDebugToggle(elements[i]);
  }
}

function sfWebDebugToggleAllLogLines(show, klass)
{
  var elements = $('.'+klass);
  var x = elements.length;
  for (var i = 0; i < x; ++i)
  {
    elements[i].style.display = show ? '' : 'none';
  }
}

function sfWebDebugShowOnlyLogLines(type)
{
  var types = new Array();
  types[0] = 'info';
  types[1] = 'warning';
  types[2] = 'error';
  for (klass in types)
  {
    var elements = $('#sfWebDebug .sfWebDebug' + types[klass].substring(0, 1).toUpperCase() + types[klass].substring(1, types[klass].length));
    var x = elements.length;
    for (var i = 0; i < x; ++i)
    {
      if ('tr' == elements[i].tagName.toLowerCase())
      {
        elements[i].style.display = (type == types[klass]) ? '' : 'none';
      }
    }
  }
}