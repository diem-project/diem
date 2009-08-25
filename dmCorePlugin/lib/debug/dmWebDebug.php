<?php

class dmWebDebug extends sfWebDebug
{
  /**
   * Configures the web debug toolbar.
   */
  public function configure()
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_cache'))
    {
      $this->setPanel('cache', new sfWebDebugPanelCache($this));
    }
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->setPanel('config', new sfWebDebugPanelConfig($this));
    }
    $this->setPanel('logs', new sfWebDebugPanelLogs($this));

    if (true || sfConfig::get('sf_debug'))
    {
      $this->setPanel('time', new dmWebDebugPanelTimer($this));
    }
  }

  /**
   * Injects the web debug toolbar into a given HTML string.
   *
   * @param string  $content The HTML content
   *
   * @return string The content with the web debug toolbar injected
   */
  public function injectToolbar($content)
  {
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
    $titles = array();
    $panels = array();
    foreach ($this->panels as $name => $panel)
    {
      if ($title = $panel->getTitle())
      {
        if (($content = $panel->getPanelContent()) || $panel->getTitleUrl())
        {
          $id = sprintf('sfWebDebug%sDetails', $name);
          $titles[]  = sprintf('<li><a title="%s" href="%s"%s>%s</a></li>',
            $panel->getPanelTitle(),
            $panel->getTitleUrl() ? $panel->getTitleUrl() : '#',
            $panel->getTitleUrl() ? '' : ' onclick="sfWebDebugShowDetailsFor(\''.$id.'\'); return false;"',
            $title
          );
          $panels[] = sprintf('<div id="%s" class="sfWebDebugTop" style="display: none"><h1>%s</h1>%s</div>',
            $id,
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
        <div id="sfWebDebugBar" class="sfWebDebug'.ucfirst($this->getPriority($this->logger->getHighestPriority())).'">
          <ul id="sfWebDebugDetails" class="sfWebDebugMenu">
            '.implode("\n", $titles).'
          </ul>
        </div>

        '.implode("\n", $panels).'
      </div>
    ';
  }
}