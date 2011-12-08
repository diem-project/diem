<div class="dm_add_behavior_menu ui-widget ui-widget-content ui-corner-top" style="display: none; border-bottom: solid 1px transparent;">
    <div class="dm_add_behavior_title">        
        <input class="dm_add_behavior_search" title="<?php echo __('Search for a behavior'); ?>" />
    </div>
    <div class="dm_behaviors_list">
        <?php foreach ($behaviors as $section): ?>
            <div class="dm_behaviors_section">
                <div class="dm_behaviors_section_title"><?php echo __($section['section_name']); ?></div>
                <div class="dm_behaviors_section_list clearfix">
                    <?php foreach ($section['behaviors'] as $key => $behavior): ?>

                    <?php include_partial('dmBehaviorsFramework/behavior_icon', array(
                        'id'        =>      $key,
                        'name'      =>      $behavior['name'],
                        'icon'      =>      $behavior['icon']
                    )); ?>

                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>