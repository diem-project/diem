<?php

if ($profile = $sf_guard_user->Profile)
{
  echo £link($profile)->text(__('Edit'));
}
else
{
  echo £link('@dm_profile_new')->param('defaults[sf_guard_user_id]', $sf_guard_user->id)->text(__('New'));
}