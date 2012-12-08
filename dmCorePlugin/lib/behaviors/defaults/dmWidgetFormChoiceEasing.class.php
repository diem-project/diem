<?php

/**
 * Easing will be commonly used by behaviors....
 * So it is wise to have a form widget and validator for that...
 * @author TheCelavi
 */
class dmWidgetFormChoiceEasing extends sfWidgetFormChoice {
    
    public static $easing = array(
        'jswing' => 'Swing',
        'easeInQuad' => 'In Quad',
        'easeOutQuad' => 'Out Quad',
        'easeInOutQuad' => 'In Out Quad',
        'easeInCubic' => 'In Cubic',
        'easeOutCubic' => 'Out Cubic', 
        'easeInOutCubic' => 'In Out Cubic',
        'easeInQuart' => 'In Quart',
        'easeOutQuart' => 'Out Quart',
        'easeInOutQuart' => 'In Out Quart',
        'easeInQuint' => 'In Quint',
        'easeOutQuint' => 'Out Quint',
        'easeInOutQuint' => 'In Out Quint',
        'easeInSine' => 'In Sine',
        'easeOutSine' => 'Out Sine',
        'easeInOutSine' => 'In Out Sine',
        'easeInExpo' => 'In Expo',
        'easeOutExpo' => 'Out Expo',
        'easeInOutExpo' => 'In Out Expo',
        'easeInCirc' => 'In Circ',
        'easeOutCirc' => 'Out Circ',
        'easeInOutCirc' => 'In Out Circ',
        'easeInElastic' => 'In Elastic',
        'easeOutElastic' => 'Out Elastic',
        'easeInOutElastic' => 'In Out Elastic',
        'easeInBack' => 'In Back',
        'easeOutBack' => 'Out Back',
        'easeInOutBack' => 'In Out Back',
        'easeInBounce' => 'In Bounce',
        'easeOutBounce' => 'In Out Bounce',
        'easeInOutBounce' => 'In Out Bounce',
        'def' => 'No easing'
    );
    
    public function __construct($options = array(), $attributes = array()) {
        if (isset($options['choices'])) $options['choices'] = array_merge($options['choices'], dmContext::getInstance()->getServiceContainer()->getService('i18n')->translateArray(dmWidgetFormChoiceEasing::$easing));
        else $options['choices'] = dmContext::getInstance()->getServiceContainer()->getService('i18n')->translateArray(dmWidgetFormChoiceEasing::$easing);
        parent::__construct($options, $attributes);
    }
}

