<?php

function renderSitemap($sitemap) {
  $result = '';

  foreach($sitemap as $entry) {
    $page = $entry['page'];

    if (!in_array($entry['visibility'], array('hidden', 'menu'))) {
      $result .= "<li><a href=\"".Toolkit::pageURL($page->getID())."\"";
      if ($entry['selected']) {
        $result .= " class=\"selected\"";
      }
      $result .= ">".htmlspecialchars($page->getProperty('title'))."</a>";

      if (count($entry['child']) > 0) {
        $childResult = renderSitemap($entry['child']);
        if (strlen($childResult) > 0) {
          $result .= "\n".RenderingUtil::indent($childResult, 2);
        }
      }
      $result .= "</li>\n";
    }
  }

  if (strlen($result) > 0) {
    $result = "<ul>\n".RenderingUtil::indent($result, 2)."</ul>\n";
  }

  return $result;
}

?>