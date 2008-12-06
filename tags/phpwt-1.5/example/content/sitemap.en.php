<?php require_once 'sitemap.inc.php'; ?>
<h1>Site Map</h1>
<?php
$sitemap = PageHierarchy::getInstance()->getHierarchy(getPage()->getID(), 3);
echo renderSitemap($sitemap);
?>
