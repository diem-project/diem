<?php
// Dm test post : Show
// Vars : $dmTestPost

echo £o('div.dm_test_post.show');

  echo
  £('h1', $dmTestPost),
  £('p.user', $dmTestPost->Author).
  £('p.excerpt', $dmTestPost->excerpt).
  £('div.body', markdown($dmTestPost->body)).
  £('p.url', £link($dmTestPost->url)).
  £('p.categ', £link($dmTestPost->Categ)).
  £('p.image', £media($dmTestPost->Image)->size(200, 200)).
  £('p.file', £link($dmTestPost->File)).
  £('p.date', $dmTestPost->date);
  
echo £c('div');