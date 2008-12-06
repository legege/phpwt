<?php
function renderSectionMenu($sitemap, $first = true) {
  $result = '';

  foreach($sitemap as $entry) {
    $page = $entry['page'];

    if ($entry['visibility'] != 'hidden') {
      $result .= "<li>";
      
      $resultTemp1 = "<a class=\"".(count($entry['child']) > 0 ? "category" : "link")."\" href=\"".Toolkit::pageURL($page->getID())."\">".htmlspecialchars($page->getProperty('title'))."</a>";
      
      if ($entry['selected']) {
        $resultTemp1 = "<span class=\"selected\">".$resultTemp1."</span>";
      }
      
      $result .= $resultTemp1;

      if (count($entry['child']) > 0) {
        $childResult = renderSectionMenu($entry['child'], false);
        if (strlen($childResult) > 0) {
          $result .= "\n".RenderingUtil::indent($childResult, 2);
        }
      }
      $result .= "</li>\n";
    }
  }

  if (!$first && strlen($result) > 0) {
    $result = "<ul>\n".RenderingUtil::indent($result, 2)."</ul>\n";
  }

  return $result;
}

$parentPageID = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'parent-page-id');
$parentPageID = (is_null($parentPageID)) ? getPage()->getID() : $parentPageID;

$showParent = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'show-parent');
$showParent = (is_null($showParent) || $showParent == 'true') ? true : false;

$showParentSiblings = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'show-parent-siblings');
$showParentSiblings = (is_null($showParentSiblings) || $showParentSiblings == 'false') ? false : true;

$sitemap = PageHierarchy::getInstance()->getHierarchy(getPage()->getID(), 0, $parentPageID, $showParent);
echo "<ul class=\"menu\">\n".RenderingUtil::indent(renderSectionMenu($sitemap), 2)."</ul>\n";
?>