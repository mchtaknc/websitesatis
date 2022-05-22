<?php
require_once(__DIR__."/_com/functions.php");
header('Content-Type: text/xml');
$themes = $db->query("select * from themes where status = 1");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '
    <url>
        <loc>' . $domain . '</loc>
    </url>
    <url>
        <loc>'.$domain.'hakkimizda</loc>
    </url>
    <url>
        <loc>'.$domain.'hazir-web-sitesi</loc>
    </url>
    <url>
        <loc>'.$domain.'blog</loc>
    </url>
    <url>
        <loc>'.$domain.'iletisim</loc>
    </url>
    <url>
        <loc>'.$domain.'banka-hesaplari</loc>
    </url>';
foreach($themes as $item) {
    echo '
    <url>
        <loc>'.$domain.$item->slug.'</loc>
    </url>';
}
echo PHP_EOL.'</urlset>';