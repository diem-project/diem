<?php

/**
 * Easing will be commonly used by behaviors....
 * So it is wise to have a form widget and validator for that...
 * @author TheCelavi
 */
class dmValidatorChoiceEasing extends sfValidatorChoice {
    
    public function __construct($options = array(), $messages = array()) {
        if (isset($options['choices'])) $options['choices'] = array_merge($options['choices'], array_keys(dmWidgetFormChoiceEasing::$easing));
        else $options['choices'] = array_keys (dmWidgetFormChoiceEasing::$easing);        
        parent::__construct($options, $messages);
    }
    
}
