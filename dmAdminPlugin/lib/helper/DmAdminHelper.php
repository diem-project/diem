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

