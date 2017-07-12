<?php // Vars: $dmTagPager

echo _open('ul.elements');

foreach ($dmTags as $dmTag)
{
  echo _open('li.element');

    $tagText = $dmTag->name.' ('.$dmTag->total_num.')';

    if($dmTag->hasDmPage())
    {
      echo _link($dmTag)->text($tagText);
    }
    else
    {
      echo $tagText;
    }

  echo _close('li');
}

echo _close('ul');