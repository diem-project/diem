<?php use_helper('Date');

$form = sprintf('%s%s%s',
  sprintf('<form action="%s">', £link('@dm_sitemap?action=generate')->getHref()),
  sprintf('<input type="submit" value="%s" />', __('Generate sitemap')),
  '</form>'
);

echo £o('div.dm_box.big.sitemap');

echo £('h1.title', __('Generate sitemap'));

echo £o('div.dm_box_inner');

if ($sitemap)
{
	echo £('div.clearfix.mb10',
		definition_list(array(
		  'Position' => £link($sitemapWebPath),
		  'Urls' => $nbLinks,
		  'Size' => $size,
		  'Updated at' => format_date($updatedAt)
		), '.clearfix.dm_little_dl.fleft.mr20').
		$form
	);
	
	echo £('pre', array('style' => 'background: #fff; padding: 10px; border: 1px solid #ddd;'), htmlentities($sitemap));
}
else
{
	echo £('p', __('There is currently no sitemap'));
	
	echo $form;
}

echo £c('div');

echo £c('div');