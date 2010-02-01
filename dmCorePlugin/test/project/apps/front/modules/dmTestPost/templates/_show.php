<?php
// Dm test post : Show
// Vars : $dmTestPost

echo _open('div.dm_test_post.show');

  echo
  _tag('h1', $dmTestPost),
  _tag('p.user', $dmTestPost->Author).
  _tag('p.excerpt', $dmTestPost->excerpt).
  _tag('div.body', markdown($dmTestPost->body)).
  _tag('p.url', _link($dmTestPost->url)).
  _tag('p.categ', _link($dmTestPost->Categ)).
  _tag('p.image', _media($dmTestPost->Image)->size(200, 200)).
  _tag('p.file', _link($dmTestPost->File)).
  _tag('p.date', $dmTestPost->date);
  
echo _close('div');