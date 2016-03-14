<?php //example: $rows['sitemap.xml'] = 'sitemap';
$rows['sitemap.xml'] = 'sitemap';

$rows['blog/login'] = 'blog/login';
$rows['blog/logout'] = 'blog/logout';
$rows['blog/admin'] = 'blog/admin';
$rows['blog/admin/(:any)'] = 'blog/admin/$1';
$rows['blog/(:any)/(:any)'] = 'blog/article';
$rows['blog/(:any)'] = 'blog/category/$1';
