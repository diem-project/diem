<?php

/**
 * @author TheCelavi
 */
class dmBehaviorsSortForm extends dmForm {
    
    protected $behaviors;


    public function __construct($behaviors, $options = array(), $CSRFSecret = null) {        
        $behaviorsManager = $this->getService('behaviors_manager');        
        $tmp = array();            
        foreach ($behaviors as $behavior) {
            try {
                $settings = $behaviorsManager->getBehaviorSettings($behavior['dm_behavior_key']);
            } catch (Exception $e) {
                throw new dmException($this->getI18n()->__('The behavior type with key %key% that you are trying to sort does not exist.', array('%key%'=>$behavior['dm_behavior_key'])));
            }
            $tmp[] = array(
                'dm_behavior_id'                    =>                  $behavior['id'],
                'dm_behavior_key'                   =>                  $behavior['dm_behavior_key'],
                'dm_behavior_name'                  =>                  $this->getI18n()->__($settings['name']),
                'dm_behavior_icon'                  =>                  $settings['icon'],
                'dm_behavior_attached_to'           =>                  $this->getI18n()->__($behavior['dm_behavior_attached_to']),
                'dm_behavior_attached_to_id'        =>                  $behavior['dm_page_id'] + $behavior['dm_area_id'] + $behavior['dm_zone_id'] + $behavior['dm_widget_id'], // null + null + null + number = number
                'dm_behavior_attached_to_selector'  =>                  $behavior['dm_behavior_attached_to_selector'],
                'dm_behavior_sequence'              =>                  $behavior['position']
            );
        }
        $this->behaviors = $tmp;
        parent::__construct(array('behaviors' => json_encode($tmp)), $options, false);
    }

    public function configure() {
        parent::configure();
        $this->widgetSchema['behaviors'] = new sfWidgetFormInputHidden();
        $this->validatorSchema['behaviors'] = new sfValidatorPass();
    }

    public function render($attributes = array()) {
        $attributes = dmString::toArray($attributes, true);
        return
                $this->open($attributes) .
                $this->renderContent($attributes) .
                $this->renderSortList() .
                $this->renderActions() .
                $this->close();
    }

    protected function renderContent($attributes) {
        return '<ul class="dm_form_elements">' . $this->getFormFieldSchema()->render($attributes) . '</ul>';
    }
    
    protected function renderSortList() {
        $helper = $this->getHelper();
        $html = $helper->open('ul.dm_behaviors_sortable');
            foreach ($this->behaviors as $behavior) {
                $html .= $helper->open('li', array('json'=>$behavior));
                    $html .= $helper->open('span.move');
                    $html .= $helper->tag('img', array('width'=>'16', 'height'=>'16', 'src'=>$behavior['dm_behavior_icon']));
                        $html .= sprintf('%s :: %s %s %s',                                 
                                $behavior['dm_behavior_name'], 
                                $behavior['dm_behavior_attached_to'],
                                $behavior['dm_behavior_attached_to_id'],
                                ($behavior['dm_behavior_attached_to_selector'] != '') ? ('> ' . $behavior['dm_behavior_attached_to_selector']) : '');
                    $html .= $helper->close('span');
                $html .= $helper->close('li');
            }
        $html .= $helper->close('ul');
        return $html;
    }


    protected function renderActions() {
        return sprintf(
            '<div class="actions">
                <div class="actions_part clearfix">%s%s</div>
                <div class="actions_part clearfix"></div>
            </div>', sprintf(
                                '<a class="dm cancel close_dialog button fleft">%s</a>', $this->__('Cancel')),
                sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', $this->__('Save'))
        );        
    }
    
    public function saveSortOrder() {
        try {
            $behaviors = json_decode($this->getValue('behaviors'), true);
            DmBehaviorTable::getInstance()->getConnection()->beginTransaction();
            foreach($behaviors as $behavior) {
                $tmp = dmDb::query('DmBehavior b')->where('id = ?', $behavior['dm_behavior_id'])->fetchOne();
                $tmp->setPosition($behavior['dm_behavior_sequence']);
                $tmp->save();
            }
            DmBehaviorTable::getInstance()->getConnection()->commit(); 
            return true;
        } catch (Exception $e) {            
            DmBehaviorTable::getInstance()->getConnection()->rollback();
            return false;
        }            
    }
    
}
