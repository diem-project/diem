<?php

/**
 * Includes Assets
 */
abstract class dmAssetFilter extends dmFilter
{
	const ENABLED = true;

	protected
	  $request,
	  $response,
	  $assets;

  protected function getJs()
  {
  	return array(
      'lib.jquery',
  	  sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
  	);
  }

  protected function getCss()
  {
  	return array(
      'lib.reset',
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null
  	);
  }

  public function jQueryUiI8n()
  {
  	return str_replace(
  	'%culture%',
  	dm::getUser()->getCulture(),
  	dmArray::get(dmAsset::getConfig(), 'js.lib.ui-i18n').'.js'
  	);
  }

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
  	$timer = dmDebug::timer('dmAssetFilter::execute');

    $request = $this->context->getRequest();

    $this->response = $this->context->getResponse();

    $has_to_work =
         self::ENABLED
      && !$request->isXmlHttpRequest()
      && !$request->isFlashRequest()
      && strpos($this->response->getContentType(), 'text/html') === 0;

    if ($has_to_work)
    {
      $this->addAssets($this->getAssets());
      //$this->response->cacheAssets();
    }

    $timer->addTime();

    $filterChain->execute();
  }

  protected function getAssets()
  {
  	return array(
  	  'js'  => $this->getJs(),
  	  'css' => $this->getCss()
  	);
  }

  protected function addAssets($types)
  {
    foreach(array('js' => 'addJavascript', 'css' => 'addStylesheet') as $type => $addMethod)
    {
      foreach(array_unique($types[$type]) as $asset)
      {
      	if ($asset)
      	{
          $this->response->$addMethod($asset, 'first');
      	}
      }
    }
  }
}