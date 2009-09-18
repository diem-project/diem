[?php
  if($<?php echo $this->getSingularName(); ?>->isNew())
  {
    return '';
  }
?]
<div class="dm_form_pagination">
  [?php
  if ($nearRecords)
  {
    if ($previousRecord = $<?php echo $this->getSingularName(); ?>->getPreviousRecord($nearRecords))
    {
      echo dmAdminLinkTag::build(array(
        '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
        $previousRecord
      ))->textTitle($previousRecord)->set('.previous.s16block.s16_previous');
    }

    $options = array();
    foreach($nearRecords as $record)
    {
      $options[$record->getPrimaryKey()] = dmString::truncate($record->__toString(), 30);
    }
    
    $recordSelect = new sfWidgetFormSelect(array('choices' => $options));
    echo $recordSelect->render('dm_select_record', $<?php echo $this->getSingularName(); ?>->getPrimaryKey(), array('id' => 'dm_select_record___DM_RANDOM_ID__', 'class' => '{ href: "'.preg_replace('|/\d+(/edit)?$|', '/_ID_/edit', $sf_request->getUri()).'"}'));
    unset($recordSelect);

    if ($nextRecord = $<?php echo $this->getSingularName(); ?>->getNextRecord($nearRecords))
    {
      echo dmAdminLinkTag::build(array(
        '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
        $nextRecord
      ))->textTitle($nextRecord)->set('.next.s16block.s16_next');
    }
  }
  ?]
</div>