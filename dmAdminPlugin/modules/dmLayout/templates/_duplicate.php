<?php

echo _link('+/dmLayout/duplicate?id='.$dm_layout->get('id'))
->set('.s16.s16_arrow_split.block')
->text(__('Duplicate'));