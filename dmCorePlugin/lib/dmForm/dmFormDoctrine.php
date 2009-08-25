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

  /**
   * Embeds i18n objects into the current form.
   *
   * @param array   $cultures   An array of cultures
   * @param string  $decorator  A HTML decorator for the embedded form
   */
  public function embedCurrentI18n($decorator = null)
  {
    if (!$this->isI18n())
    {
      throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $class = $this->getI18nFormClass();

    $culture = dm::getUser()->getCulture();

    $i18nObject = $this->object->Translation[$culture];
    $i18n = new $class($i18nObject);
    unset($i18n['id'], $i18n['lang']);

    $this->embedForm('translation', $i18n, $decorator);
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