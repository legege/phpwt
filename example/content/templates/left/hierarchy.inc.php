<?php
function renderSectionMenu($sitemap, $first = true) {
  $result = '';

  foreach ($sitemap as $entry) {
    $page = $entry['page'];

    if ($entry['visibility'] != 'hidden') {
      $result .= "<li>";
      
      $resultTemp1 = "<a class=\"".(count($entry['child']) > 0 ? "category" : "link")."\" href=\"".Toolkit::pageURL($page->getID())."\">".htmlspecialchars($page->getProperty('title'))."</a>";
      
      if ($entry['selected']) {
        $resultTemp1 = "<span class=\"selected\">".$resultTemp1."</span>";
      }
      
      $result .= $resultTemp1;

      if (count($entry['child']) > 0 && ($entry['selected'] || $entry['child-selected'])) {
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
$parentPageIDs = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'parent-page-ids');
$parentPageID = (is_null($parentPageID) and is_null($parentPageIDs)) ? getPage()->getID() : $parentPageID;

$parentPageIDList = preg_split("/,/", $parentPageIDs);
$parentPageIDList = array_merge($parentPageIDList, Array($parentPageID));
$parentPageIDList = array_unique($parentPageIDList);

$showParent = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'show-parent');
$showParent = (is_null($showParent) || $showParent == 'true') ? true : false;

$showParentSiblings = getTemplate()->getSectionProperty(getTemplate()->getCurrentSectionName(), 'show-parent-siblings');
$showParentSiblings = (is_null($showParentSiblings) || $showParentSiblings == 'false') ? false : true;

foreach($parentPageIDList as $id) {
  if(is_null($id) or $id == "") {
    continue;
  }

  $sitemap = PageHierarchy::getInstance()->getHierarchy(getPage()->getID(), 1, $id, $showParent, $showParentSiblings);
  echo "<ul class=\"menu\">\n".RenderingUtil::indent(renderSectionMenu($sitemap), 2)."</ul>\n";
}
?>
