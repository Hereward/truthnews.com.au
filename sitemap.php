<?php
$current_date = date("Y-m-d");
$db_name = "truthnews";
$db_host = "mysql.planetonline.com.au";
$db_user = "truthnews";
$db_pass = "pullit911";
$test = FALSE;

if ($test) {
    $db_name = "";
    $db_host = "";
    $db_user = "";
    $db_pass = "";
}

$url_stem = 'http://www.truthnews.com.au';
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?>';

$url_tpl = '<url>
    <loc>%s</loc>
    <lastmod>%s</lastmod>
    <changefreq>%s</changefreq>
    <priority>%s</priority>
</url>';

$top_list = array("boo"=>"egg");


$top_list_str = "
$url_stem/web/radio,
$url_stem/news,
$url_stem/contact,
$url_stem/promote,
$url_stem/contribute,
$url_stem/subscribe,
$url_stem/radio-archives,
$url_stem/forum
";

$top_list = explode(',', $top_list_str);

?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc><?php echo $url_stem ?></loc>
        <lastmod><?=$current_date?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.00</priority>
    </url>


<?php

    foreach ($top_list as $url_raw) {
        $loc = htmlspecialchars(trim($url_raw));
        $output = sprintf($url_tpl,$loc,$current_date,'weekly','1.00');
        echo "$output\n";
    }
?>


</urlset>



