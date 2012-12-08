<?php
/**
 * @author TheCelavi
 */
class dmTipsyBehaviorForm extends dmBehaviorBaseForm {
    
    protected $gravity = array(
        'n' => 'North',
        's' => 'South',        
        'w' => 'West',
        'e' => 'East',        
        'nw' => 'Northwest',
        'ne' => 'Northeast',
        'sw' => 'Southwest',
        'se' => 'Southeast',        
        'ans' => 'Auto North-South',
        'awe' => 'Auto West-East'
    );    
    
    /**
     * Add more attributes if you want
     * It would be convinient to have metadata support and metadata behavior where you can set metadata for some HTML element!!!
     * TODO - Add metadata support
     */
    protected $textSource = array(
        'alt' => 'Alt attribute',
        'title' => 'Title attribute',
        'rel' => 'Rel attribute'        
    );
    
    protected $trigger = array(
        'hover' => 'Mouse over',
        'focus' => 'Element focus',
        'manual' => 'Manual'
    );
    
    public function configure() {
        $this->widgetSchema['inner_target'] = new sfWidgetFormInputText();
        $this->validatorSchema['inner_target'] = new sfValidatorString(array(
            'required' => false
        ));
        
        $this->widgetSchema['title'] = new sfWidgetFormChoice(array(
            'choices'=>$this->getI18n()->translateArray($this->textSource)
        ));
        $this->validatorSchema['title'] = new sfValidatorChoice(array(
            'choices'=> array_keys($this->textSource)
        ));
        
        $this->widgetSchema['gravity'] = new sfWidgetFormChoice(array(
            'choices'=>$this->getI18n()->translateArray($this->gravity)
        ));
        $this->validatorSchema['gravity'] = new sfValidatorChoice(array(
            'choices'=> array_keys($this->gravity)
        ));
        
        $this->widgetSchema['opacity'] = new sfWidgetFormInputText();
        $this->validatorSchema['opacity'] = new sfValidatorInteger(array(
            'min'=>0,
            'max'=>100
        ));   
        
        $this->widgetSchema['trigger'] = new sfWidgetFormChoice(array(
            'choices'=>$this->getI18n()->translateArray($this->trigger)
        ));
        $this->validatorSchema['trigger'] = new sfValidatorChoice(array(
            'choices'=> array_keys($this->trigger)
        ));
        
        $this->widgetSchema['fade'] = new sfWidgetFormInputCheckbox();
        $this->validatorSchema['fade'] = new sfValidatorBoolean();
        
        $this->widgetSchema['delayIn'] = new sfWidgetFormInputText();
        $this->validatorSchema['delayIn'] = new sfValidatorInteger(array(
            'min'=>0
        ));
        
        $this->widgetSchema['delayOut'] = new sfWidgetFormInputText();
        $this->validatorSchema['delayOut'] = new sfValidatorInteger(array(
            'min'=>0
        ));
        
        $this->getWidgetSchema()->setLabels(array(
            'inner_target'=> 'Inner targets',
            'title'=>'Text source',
            'trigger' => 'Event',
            'delayIn' => 'Delay in',
            'delayOut' => 'Delay out'
        ));
        
        $this->getWidgetSchema()->setHelps(array(
            'inner_target' => 'You can enter several selectors separated with comma (,)',
            'title'=>'Read text from which source?',
            'gravity'=>'The position of the tip',
            'fade' => 'Use fade animation to display tip?'
        )); 
        
        if (!$this->getDefault('inner_target')) $this->setDefault ('inner_target', 'img');
        if (!$this->getDefault('title')) $this->setDefault ('title', 'title');
        if (!$this->getDefault('gravity')) $this->setDefault ('gravity', 'ans');
        if (!$this->getDefault('opacity')) $this->setDefault ('opacity', 80);        
        if (!$this->getDefault('fade')) $this->setDefault ('fade', true);        
        if (!$this->getDefault('delayIn')) $this->setDefault ('delayIn', 0);
        if (!$this->getDefault('delayOut')) $this->setDefault ('delayOut', 0);
        
        parent::configure();
    }
    
}

