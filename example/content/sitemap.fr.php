<?php require_once 'sitemap.inc.php'; ?>
<h1>Plan du site</h1>
<?php
$sitemap = PageHierarchy::getInstance()->getHierarchy(getPage()->getID(), 2);
echo renderSitemap($sitemap);
?>
