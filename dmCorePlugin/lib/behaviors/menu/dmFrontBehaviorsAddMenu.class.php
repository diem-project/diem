<?php

/**
 * @author TheCelavi
 */
class dmFrontBehaviorsAddMenu extends dmMenu {
    
    public function build() {
        $this->name('Front add menu')
                ->ulClass('ui-helper-reset level0')
                ->addChild('Behaviors')
                ->setOption('root_add', true)
                ->ulClass('ui-widget ui-widget-content level1')
                ->liClass('ui-corner-bottom ui-state-default')                
                ->addClipboard()
                ->addBehaviors();                
                  
        $this->serviceContainer->getService('dispatcher')->notify(new sfEvent($this, 'dm.front.behavior_menu', array()));        
        return $this;
    }
    
    public function addBehaviors() {
        $helper = $this->getHelper();
        $behaviorsManager = $this->serviceContainer->getService('behaviors_manager');
        $behaviors = $behaviorsManager->getBehaviorsMenuItems();
        foreach ($behaviors as $key => $section) {
            $spaceMenu = $this->addChild($key)
                    ->label($this->__($section['section_name']))
                    ->ulClass('clearfix level2')
                    ->liClass('dm_droppable_behaviors');
            foreach ($section['behaviors'] as $k => $behavior) {
                $spaceMenu->addChild($k)
                        ->label($this->getI18n()->__($behavior['name']))
                        ->setOption('behavior_key', $k)
                        ->setOption('behavior_icon', $behavior['icon']); // TODO - TheCelavi - how to parse path to icon?
            }
            if(!$spaceMenu->hasChildren()){
                $this->removeChild($spaceMenu);
            }
        }
        return $this;
    }
    
    public function addClipboard() {
        $user = $this->serviceContainer->getService('user');
        if (!$user->can('behavior_add')) return $this;
        $data = $user->getAttribute('dm_behavior_clipboard', null, 'dm.front_user_behavior_clipboard');
        if (!is_null($data)) {
            if ($data['dm_behavior_clipboard_action'] == 'cut' && !$user->can('behavior_delete')) return $this; 
            $behaviorsManager = $this->serviceContainer->getService('behaviors_manager');
            try {                
                $behavior = DmBehaviorTable::getInstance()->findOneById($data['dm_behavior_id']);
                if (!$behavior) return $this;
                $settings = $behaviorsManager->getBehaviorSettings($behavior->getDmBehaviorKey());
            } catch (Exception $e) {
                return $this;
            }
            $this->addChild('Clipboard')->ulClass('clearfix level2')->liClass('dm_droppable_behaviors')
            ->addChild($behavior->getDmBehaviorKey() . 'clipboard')
            ->setOption('clipboard_action', $data['dm_behavior_clipboard_action'])
            ->setOption('clipboard_icon', $settings['icon'])
            ->setOption('clipboard_id', $behavior->getId())
            ->label($settings['name']);
        }
        return $this;
    }

  public function renderLabel()
  {
    if($this->getOption('behavior_key'))
    {
      return sprintf('<span class="behavior_add move" id="dmba_%s"><img src="%s" width="16" height="16" />%s</span>',        
        $this->getOption('behavior_key'),
        $this->getOption('behavior_icon'),              
        parent::renderLabel()
      );
    }
    elseif($this->getOption('clipboard_action'))
    {
      return sprintf('<span class="behavior_add clipboard %s move" id="dmba_clipboard_behavior_id_%s"><img src="%s" width="16" height="16" />%s</span>',        
        $this->getOption('clipboard_action'),
        $this->getOption('clipboard_id'),
        $this->getOption('clipboard_icon'),
        dmString::strtolower(parent::renderLabel())
      );
    }
    elseif($this->getOption('root_add'))
    {
      return '<a class="tipable s24block s24_gear widget24" title="'.$this->__('Add behaviors').'"></a>';
    }
    
    return '<a>'.dmString::strtolower(parent::renderLabel()).'</a>';
  }

}

