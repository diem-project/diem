<?php

/**
 * Diem form base class.
 *
 * @package    diem
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormBaseTemplate.php 9304 2008-05-27 03:49:32Z dwhittle $
 */
abstract class dmFormDoctrine extends sfFormDoctrine
{
	protected
	$key,
	$name;

	public function setup()
	{
		$this->widgetSchema->setFormFormatterName('dmList');

		$this->key = "dm_form_doctrine_".dmString::random(6);

		$this->name = dmString::underscore($this->getModelName());
	}

	public function getKey() { return $this->key; }

	/**
	 * Renders the widget schema associated with this form.
	 *
	 * @param array $attributes An array of HTML attributes
	 *
	 * @return string The rendered widget schema
	 */
	public function render($attributes = array())
	{
		$attributes = dmString::toArray($attributes, true);
		sfProjectConfiguration::getActive()->loadHelpers(array('Form'));
		return
		$this->open($attributes).
    '<ul class="dm_form_elements">'.
		$this->getFormFieldSchema()->render($attributes).
		sprintf('<li class="dm_form_element"><label>%s</label>%s</li>', dm::getI18n()->__('Validate'), submit_tag(dm::getI18n()->__('Save'))).
    '</ul>'.
		$this->close();
	}

	/*
	 * utilise automatiquement la requete en cours
	 * @see lib/form/sfForm#bind()
	 */
	public function bind(array $taintedValues = null, array $taintedFiles = null)
	{
		$taintedValues = !empty($taintedValues) ? $taintedValues : dm::getRequest()->getParameter($this->name);
		$taintedFiles = !empty($taintedFiles) ? $taintedFiles : dm::getRequest()->getFiles($this->name);

		$return = parent::bind($taintedValues, $taintedFiles);

		return $return;
	}

	protected function filterValuesByEmbeddedMediaForm(array $values, $local)
	{
		$formName = $local.'_form';
		 
		//no existing media, no file, and it is not required : skip all
		if ($this->embeddedForms[$formName]->getObject()->isNew() && !$values[$formName]['file']['size'] && !$this->embeddedForms[$formName]->getValidator('file')->getOption('required'))
		{
			// remove the embedded media form if the file field was not provided
			unset($this->embeddedForms[$formName], $values[$formName]);
			// pass the media form validations
			$this->validatorSchema[$formName] = new sfValidatorPass;
		}

		return $values;
	}

	protected function processValuesForEmbeddedMediaForm(array $values, $local)
	{
    $formName = $local.'_form';

    if (!isset($this->embeddedForms[$formName]))
    {
      return $values;
    }

    // uploading a file
		if($values[$formName]['file'])
		{
      /*
       * We have a media with same folder / filename
       * let's use it
       */
			if ($existingMedia = $this->embeddedForms[$formName]->fileAlreadyExists())
			{
			  $values[$formName]['id'] = $existingMedia->id;
			  unset($values[$formName['file']]);
			  
        $this->embeddedForms[$formName]->setObject($existingMedia);
			}
	    /*
	     * We have a new file for an existing media.
	     * Let's create a new media
	     */
			elseif($values[$formName]['id'])
			{
				$values[$formName]['id'] = null;
				
				$media = new DmMedia;
				$media->Folder = $this->object->getDmMediaFolder();
	
	      $this->embeddedForms[$formName]->setObject($media);
			}
		}
		
		return $values;
	}

	protected function doUpdateObjectForEmbeddedMediaForm(array $values, $local, $alias)
	{
		$formName = $local.'_form';

		if (!isset($this->embeddedForms[$formName]))
		{
			return;
		}

		if (!empty($values[$formName]['remove']))
		{
			$this->object->set($alias, null);
		}
		else
		{
			$this->object->set($alias, $this->embeddedForms[$formName]->getObject());
		}
	}

	public function open($opt = array())
	{
		$opt = dmString::toArray($opt, true);
		 
		$defaults = array(
      "class" => implode(" ", array("validate_me", $this->name, dmArray::get($opt, 'class'))),
      "id" => $this->getKey()
		);

		if (isset($opt['class']))
		{
			unset($opt['class']);
		}

		$opt = array_merge($defaults, dmString::toArray($opt));

		$request = dm::getRequest();

		$action = dmArray::get($opt, "action");

		if ($action = dmArray::get($opt, "action"))
		{
			if ($action{0} == "#")
			{
				$action = dm::getRequest()->getUri().$action;
			}
		}
		else
		{
			$action = dm::getRequest()->getUri();
		}

		if (strpos($action, "#") === false)
		{
			$action .= "#".$this->getKey();
		}

		if (isset($opt["action"])) unset($opt["action"]);

		if ($this->isMultipart())
		{
			$opt["multipart"] = true;
		}

		sfProjectConfiguration::getActive()->loadHelpers(array('Form', "Tag", "Url"));

		return form_tag($action, $opt);
	}

	public function close()
	{
		return '</form>';
	}


	/*
	 * Create current i18n form
	 */
	protected function createCurrentI18nForm()
	{
		if (!$this->isI18n())
		{
			throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
		}

		$i18nFormClass = $this->getI18nFormClass();

		$culture = dm::getUser()->getCulture();

		$i18nObject = $this->object->Translation[$culture];
		$i18nForm = new $i18nFormClass($i18nObject);
		unset($i18nForm['id'], $i18nForm['lang']);

		return $i18nForm;
	}

	/**
	 * Sets the current object for this form.
	 *
	 * @return BaseObject The current object setted.
	 */
	public function setObject(myDoctrineRecord $record)
	{
		return $this->object = $record;
	}
}