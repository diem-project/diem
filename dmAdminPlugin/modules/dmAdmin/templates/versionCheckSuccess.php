<?php

echo £('div.dm_version_check_upgrade.ui-corner-all', array('title' => __('Close')),
  __('Diem %version% is available. Upgrade is recommended: %link%', array(
    '%version%' => $versionCheck->getRecommendedUpgrade(),
    '%link%'    => £link('http://diem-project.org/download')
  ))
);