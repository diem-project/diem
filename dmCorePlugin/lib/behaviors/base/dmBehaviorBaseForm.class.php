<?php

/**
 * Description of dmBehaviorForm
 *
 * @author TheCelavi
 */
abstract class dmBehaviorBaseForm extends dmForm {

    protected
    $dmBehavior,
    $stylesheets = array(),
    $javascripts = array();

    public function __construct($behavior, $options = array(), $CSRFSecret = null) {
        if (!$behavior instanceof DmBehavior) {
            throw new dmException(sprintf('%s must be initialized with a DmBehavior, not a %s', get_class($this), gettype($behavior)));
        }
        $this->dmBehavior = $behavior;
        // disable CSRF protection
        parent::__construct(array_merge($behavior->getValues(), array('dm_behavior_enabled'=> $behavior->getDmBehaviorEnabled())), $options, false);
    }

    public function setup() {
        parent::setup();
        $this->setName($this->name . '_' . $this->dmBehavior->get('id'));
    }

    public function configure() {                
        parent::configure();
        $this->widgetSchema['dm_behavior_enabled'] = new sfWidgetFormInputCheckbox(array('label' => 'Enabled'));
        $this->validatorSchema['dm_behavior_enabled'] = new sfValidatorBoolean();        
    }

    public function getDmBehavior() {
        return $this->dmBehavior;
    }

    /**
     * Overload this method to alter form values
     * when form has been validated
     */
    public function getBehaviorValues() {
        $values = $this->getValues();
        unset($values['dm_behavior_enabled']);
        return $values;
    }

    public function render($attributes = array()) {
        $attributes = dmString::toArray($attributes, true);
        return
                $this->open($attributes) .
                $this->renderContent($attributes) .
                $this->renderActions() .
                $this->close();
    }

    protected function renderContent($attributes) {
        return '<ul class="dm_form_elements">' . $this->getFormFieldSchema()->render($attributes) . '</ul>';
    }

    protected function renderActions() {
        return sprintf(
                        '<div class="actions">
                <div class="actions_part clearfix">%s%s</div>
                <div class="actions_part clearfix">%s%s</div>
            </div>', 
            sprintf('<a class="dm cancel close_dialog button fleft">%s</a>', $this->__('Cancel')), 
            $this->getService('user')->can('behavior_edit') ? sprintf('<input type="submit" class="submit try blue fright" name="try" value="%s" />', $this->__('Try')) : '', 
            $this->getService('user')->can('behavior_delete') ? sprintf('<a class="dm delete button red fleft" title="%s">%s</a>', $this->__('Delete this behavior'), $this->__('Delete')) : '', 
            $this->getService('user')->can('behavior_edit') ? sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', $this->__('Save and close')) : ''
        );
    }

    /**
     * Try to guess default values
     * from last updated behavior
     * @return array default values
     */
    protected function getDefaultsFromLastUpdated(array $fields = array()) {
        if ($this->dmBehavior->getDmBehaviorValue() != '') {
            return array_merge($this->dmBehavior->getDmBehaviorValue(), array('dm_behavior_enabled' => $this->dmBehavior->getDmBehaviorEnabled()));
        }

        $lastBehaviorValue = dmDb::query('DmBehavior b')
                ->withI18n()
                ->where('b.dm_behavior_key = ?', array($this->dmBehavior->getDmBehaviorKey()))
                ->orderBy('b.updated_at desc')
                ->limit(1)
                ->select('b.id, bTranslation.dm_behavior_value as value')
                ->fetchOneArray();
        if (!$lastBehaviorValue) {
            return array();
        }
        return json_decode((string) $lastBehaviorValue['value'], true);        
    }

    protected function getFirstDefaults() {
        return array();
    }

    protected function getFirstDefault($key) {
        return dmArray::get($this->getFirstDefaults(), $key);
    }

    public function updateBehavior() {
        $this->dmBehavior->setValues($this->getBehaviorValues());

        if (isset($this['dm_behavior_enabled'])) {
            $this->dmBehavior->set('dm_behavior_enabled', $this->getValue('dm_behavior_enabled'));
        }

        return $this->dmBehavior;
    }
    
    public function getDefaults() {
        $defaults = $this->getDefaultsFromLastUpdated();
        if (count($defaults) > 0) return $defaults;
        $defaults = $this->getFirstDefaults();
        if (count($defaults) > 0) return $defaults;
        $defaults = parent::getDefaults();
        unset($defaults['dm_behavior_enabled']);
        return $defaults;
    }

}

