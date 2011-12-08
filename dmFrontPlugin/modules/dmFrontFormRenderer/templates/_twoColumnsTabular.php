<div class="front-form-renderer two-columns-tabular">
    <ul>
    <?php 
        for($i=0; $i<count($sections); $i++) { 
            $section = $sections[$i];        
    ?>
        <li><a href="#tabs-<?php echo dmString::slugify($section->getSectionLabel()); ?>"><?php echo __($section->getSectionLabel()); ?></a></li>	
    <?php } ?>
    </ul>
    <?php 
        for($i=0; $i<count($sections); $i++) { 
            $section = $sections[$i];        
    ?>
    <div id="tabs-<?php echo dmString::slugify($section->getSectionLabel()); ?>">
        <table>            
            <?php
                $fields = $section->getFields();
                for($j=0; $j<count($fields); $j++) {                     
            ?>
            <?php 
                    if ($fields[$j]['is_big']) { 
            ?>                
            <tr>
                <td colspan="4" class="errors is_big">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderError();?>
                </td>                    
            </tr>
            <tr>
                <td class="label is_big">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderLabel();?>
                </td>
                <td colspan="3" class="field is_big">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->render();?>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="help is_big">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderHelp();?>
                </td>                    
            </tr>
            <?php
                    continue;
                }
            ?>
            <?php 
                    if ($fields[$j]['type'] == 'subsection') { 
            ?>                
            <tr>
                <td colspan="4">
                    <div class="ui-widget-header ui-corner-all">
                        <?php echo __($fields[$j]['name']); ?>
                    </div>
                </td>                    
            </tr>            
            <?php 
                    continue;
                }
            ?>
            <?php 
                    if ($fields[$j]['type'] == 'separator') { 
            ?>                
            <tr>
                <td colspan="4">
                    <hr/>
                </td>                    
            </tr>            
            <?php 
                    continue;
                }
            ?>
            <tr>
                <td colspan="2" class="errors">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderError();?>
                </td>
                <td colspan="2" class="errors">
                    <?php if ($j+1<count($fields) && !$fields[$j+1]['is_big']) if ($fields[$j+1]['type'] != 'empty') echo $form[$fields[$j+1]['name']]->renderError(); ?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderLabel();?>
                </td>
                <td class="field">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->render();?>
                </td>
                <td class="label">
                    <?php if ($j+1<count($fields) && !$fields[$j+1]['is_big']) if ($fields[$j+1]['type'] != 'empty') echo $form[$fields[$j+1]['name']]->renderLabel(); ?>
                </td>
                <td class="field">
                    <?php if ($j+1<count($fields) && !$fields[$j+1]['is_big']) if ($fields[$j+1]['type'] != 'empty') echo $form[$fields[$j+1]['name']]->render(); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="help">
                    <?php if ($fields[$j]['type'] != 'empty') echo $form[$fields[$j]['name']]->renderHelp();?>
                </td>
                <td colspan="2" class="help">
                    <?php if ($j+1<count($fields) && !$fields[$j+1]['is_big']) if ($fields[$j+1]['type'] != 'empty') echo $form[$fields[$j+1]['name']]->renderHelp(); ?>
                </td>
            </tr>
            <?php
                    if ($j+1<count($fields) &&  !$fields[$j+1]['is_big']) $j++;
                } 
            ?>            
        </table>
        <?php if (!is_null($section->getSectionHelp())) : ?>
        <div class="dm_section_help">
            <?php echo __($section->getSectionHelp()); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php } echo $form->renderHiddenFields(); ?>
</div>