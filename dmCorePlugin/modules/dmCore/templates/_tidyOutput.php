<?php

if ($output)
{
	echo £("a.widget16.dm_tidy_output.s16.s16_bandaid title='Tidy output'",
    substr_count($output, "\n") + 1
	).
	£("div#dm_tidy_output",
	  £("p.list", nl2br(htmlspecialchars($output))).
    '<hr />'.
	  £("p.sprite_16.sprite_16_lightbulb_on", sfConfig::get('dm_tidy_replace')
	    ? __('Tidy improvments have been applied to the source code')
	    : __('Tidy improvments have not been applied to the source code')
	  )
  );
}
else
{
	echo £("a.widget16.dm_tidy_output.s16block.s16_trophy title='".__('This source code seems perfect')."'", "0").
	£("div#dm_tidy_output",
	  £("p.s24.s24_tick", __('Tidy found nothing to improve'))
	);
}