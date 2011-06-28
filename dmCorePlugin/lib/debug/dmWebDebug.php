<?php

class dmWebDebug extends sfWebDebug
{

  /**
   * Injects the web debug toolbar into a given HTML string.
   *
   * @param string  $content The HTML content
   *
   * @return string The content with the web debug toolbar injected
   */
  public function injectToolbar($content)
  {
    // we don't want to show web debug panel on non-html response
    if (sfConfig::get('dm_web_debug_only_html_response', true) && !strpos($content, '</html>'))
    {
      return $content;
    }
    
    $debug = $this->asHtml();
    if (strpos($content, '__SF_WEB_DEBUG__'))
    {
      $content = str_replace('__SF_WEB_DEBUG__', $debug, $content);
    }
    else
    {
      $count = 0;
      $content = str_ireplace('</body>', $debug.'</body>', $content, $count);
      if (!$count)
      {
        $content .= $debug;
      }
    }

    return $content;
  }

  /**
   * Returns the web debug toolbar as HTML.
   *
   * @return string The web debug toolbar HTML
   */
  public function asHtml()
  {
    $current = isset($this->options['request_parameters']['sfWebDebugPanel']) ? $this->options['request_parameters']['sfWebDebugPanel'] : null;

    $titles = array();
    $panels = array();
    foreach ($this->panels as $name => $panel)
    {
      if ($title = $panel->getTitle())
      {
        if (($content = $panel->getPanelContent()) || $panel->getTitleUrl())
        {
          $id = sprintf('sfWebDebug%sDetails', $name);
          $titles[] = sprintf('<li class="%s"><a title="%s" href="%s"%s>%s</a></li>',
            $panel->getStatus() ? 'sfWebDebug'.ucfirst($this->getPriority($panel->getStatus())) : '',
            $panel->getPanelTitle(),
            $panel->getTitleUrl() ? $panel->getTitleUrl() : '#',
            $panel->getTitleUrl() ? '' : ' onclick="sfWebDebugShowDetailsFor(\''.$id.'\'); return false;"',
            $title
          );
          $panels[] = sprintf('<div id="%s" class="sfWebDebugTop" style="display:%s"><h1>%s</h1>%s</div>',
            $id,
            $name == $current ? 'block' : 'none',
            $panel->getPanelTitle(),
            $content
          );
        }
        else
        {
          $titles[] = sprintf('<li>%s</li>', $title);
        }
      }
    }

    return '
      <div id="sfWebDebug">
        <div id="sfWebDebugBar">
          <ul id="sfWebDebugDetails" class="sfWebDebugMenu">
            '.implode("\n", $titles).'
          </ul>
        </div>

        '.implode("\n", $panels).'
      </div>
    ';
  }
}