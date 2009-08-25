<?php

function £link($source = null)
{
  return dmAdminLinkTag::build($source);
}


/*
 * Usedby dmAdminGenerator forms
 * Called to show a form part when field is not a form widget
 * ex : image_view must display an image previsualisation
 */
function dm_admin_form_field($name, $form)
{
	if (substr($name, -5) === '_view')
	{
		$fieldName = substr($name, 0, strlen($name)-5);
	  if ($relation = dmDb::table($form->getModelName())->getRelationHolder()->getLocalByColumnName($fieldName))
	  {
	  	if ($relation['class'] === 'DmMedia')
	  	{
	  		$alias = $relation['alias'];
	  		if ($media = $form->getObject()->$alias)
	  		{
	  			return get_partial('dmMedia/viewBig', array('object' => $media));
	  		}
	  		else
	  		{
	  			return '';
	  		}
	  	}
	  }
	}
	throw new dmException($name.' is not a valid form field');
}

function dm_admin_get_javascript_configuration()
{
  $dmContext = dmContext::getInstance();
  $request   = sfContext::getInstance()->getRequest();
  
  $jsConfig = sprintf('
<script type="text/javascript">
var dm_configuration = {
  relative_url_root:"%s",
  dm_core_asset_root:"%s",
  script_name:"%s",
  base_url:"%s",
  urchin_tracker:"%s",
  enable_tracking:%s,
  is_localhost:%s,
  debug:%s,
  app:"%s",
  is_working_copy:%s,
  culture:"%s",
  module:"%s"
};
</script>',
    $request->getRelativeUrlRoot()."/",
    $request->getRelativeUrlRoot()."/".sfConfig::get('dm_core_asset')."/",
    $request->getScriptName()."/",
    $request->getUriPrefix()."/",
    $dmContext->getSite()->urchinTrackerActive ? $dmContext->getSite()->urchinTracker : "",
    (dmOs::isLocalhost() || dm::getUser()->hasCredential("admin")) ? "false" : "true",
    dmOs::isLocalhost() ? "true" : "false",
    (sfConfig::get('sf_debug') || $request->hasParameter("debug_js")) ? "true" : "false",
    sfConfig::get("sf_app"),
    $dmContext->getSite()->isWorkingCopy ? "true" : "false",
    dm::getUser()->getCulture(),
    $dmContext->getModule()
  );

  if (!sfConfig::get('sf_debug'))
  {
    $jsConfig = str_replace(array("\n", "\t", "  "), " ", $jsConfig);
  }

  return $jsConfig;
}