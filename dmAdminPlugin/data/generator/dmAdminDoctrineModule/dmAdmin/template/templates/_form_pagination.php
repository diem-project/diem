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
	  use_helper('Form');
	  if ($previousRecord = $<?php echo $this->getSingularName(); ?>->getPreviousRecord($nearRecords))
	  {
	    echo dmAdminLinkTag::build(array(
	      '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
	      $previousRecord
	    ))->nameTitle($previousRecord)->set('.previous.s16block.s16_previous');
	  }

    $options = array();
    foreach($nearRecords as $record)
    {
      $options[$record->getPrimaryKey()] = dmString::truncate($record->__toString(), 30);
    }
    echo str_replace(' id="dm_select_object"', '', select_tag(
      'dm[select_object]',
      options_for_select($options, $<?php echo $this->getSingularName(); ?>->getPrimaryKey()),
      array('class' => '{ href: "'.preg_replace('|\d+(/edit)?$|', '_ID_/edit', $sf_request->getUri()).'"}')
    ));

	  if ($nextRecord = $<?php echo $this->getSingularName(); ?>->getNextRecord($nearRecords))
	  {
	    echo dmAdminLinkTag::build(array(
	      '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
	      $nextRecord
	    ))->nameTitle($nextRecord)->set('.next.s16block.s16_next');
	  }
  }
  ?]
</div>