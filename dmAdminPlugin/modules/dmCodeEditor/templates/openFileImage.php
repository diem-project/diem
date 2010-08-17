<?php

echo _tag('div.file_tab.inner',
  _tag('div.inner_border',
    _tag('div.image', $file['media']->width(600))
  )
);