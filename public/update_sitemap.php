<?php

$dbs = new mysqli('localhost', 'u0474956_user', 'xG9pW1sW8trQ3lY5', 'u0474956_glaza71');
if ($dbs->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $dbs->connect_errno . ") " . $dbs->connect_error;
}
$dbs->query('SET names "utf8"');
//$today = (new \DateTime('now'))->format('Y-m-d H:i:s');
$today = date('Y-m-d');
$url = 'https://glaza71.ru';
$mainPages = [
    ['url' => '', 'priority' => '1'],
    ['url' => '/optic', 'priority' => '1'],
    ['url' => '/tomography', 'priority' => '1'],
    ['url' => '/articles', 'priority' => '0.8'],
];
$result = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$result .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
foreach ($mainPages as $page) {
    write($page['priority'], $today, $url . $page['url'], $result);
}
$request = 'SELECT * FROM `article` WHERE published_at IS NOT NULL';
$res = $dbs->query($request);
while ($row = mysqli_fetch_assoc($res)) {
    write('0.9', $today, $url . '/article/' . $row['slug'], $result);
}

$result .= '</urlset>';

$fileName = 'sitemap.xml';
file_put_contents($fileName, $result);

function write($priority, $today, $link, &$res): void
{
    $res .= "  <url>" . PHP_EOL;
    $res .= "    <loc>" . $link . "</loc>" . PHP_EOL;
    $res .= "    <priority>$priority</priority>" . PHP_EOL;
    $res .= "    <lastmod>" . $today . "</lastmod>" . PHP_EOL;
    $res .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
    $res .= "  </url>" . PHP_EOL;
}
