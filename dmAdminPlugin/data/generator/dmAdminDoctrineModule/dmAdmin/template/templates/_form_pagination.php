<div class="dm_form_pagination">
  [?php
  if ($nearRecords)
  {
    if ($previousRecord = $nearRecords['prev'])
    {
      echo dmAdminLinkTag::build(array(
        '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
        $previousRecord
      ))->textTitle($previousRecord)->set('.previous.s16block.s16_previous');
    }

    if ($nextRecord = $nearRecords['next'])
    {
      echo dmAdminLinkTag::build(array(
        '<?php echo $this->getModule()->getUnderscore(); ?>_edit',
        $nextRecord
      ))->textTitle($nextRecord)->set('.next.s16block.s16_next');
    }
  }
  ?]
</div>