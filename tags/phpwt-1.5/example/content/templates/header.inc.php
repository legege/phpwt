<?php
$title = getPage()->getProperty('title');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Toolkit::getInstance()->getLanguage(); ?>">

<head>
  <title>Example.com<?php echo (strlen($title) > 0) ? ' - '.$title : ''; ?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="icon" href="<?php echo Toolkit::resourceURL('/media/images/favicon.png'); ?>" type="image/x-icon" />
  <link rel="stylesheet" href="<?php echo Toolkit::resourceURL('/media/css/screen.css'); ?>" type="text/css" media="screen" />
</head>

<body>

<div id="header">
  <h1><a href="<?php echo Toolkit::pageURL('/home'); ?>">Example.com</a></h1>

  <div id="lang">
    <ul>
      <li><a href="<?php echo Toolkit::pageURL(getPage()->getID(), 'en'); ?>">English</a></li>
      <li><a href="<?php echo Toolkit::pageURL(getPage()->getID(), 'fr'); ?>">Français</a></li>
    </ul>
  </div>

  <div id="menu">
<?php
$menu = PageHierarchy::getInstance()->getRootPages(getPage()->getID());

$result = '';
foreach ($menu as $entry) {
  $page = $entry['page'];

  if ($entry['visibility'] != 'hidden') {
    $result .= "  <li><a href=\"".Toolkit::pageURL($page->getID())."\"";
    if ($entry['selected']) {
      $result .= " class=\"selected\"";
    }
    else if ($entry['child-selected']) {
      $result .= " class=\"child-selected\"";
    }
    $result .= ">".htmlspecialchars($page->getProperty('title'))."</a></li>\n";
  }
}

if (strlen($result) > 0) {
  echo RenderingUtil::indent("<ul class=\"mainmenu\">\n".$result."</ul>\n", 4);
}
?>
  </div>
</div>

<div id="left">
<?php getTemplate()->includeSection('left'); ?>
</div>

<div id="navigation">Navigation: <?php
$navigation = PageHierarchy::getInstance()->getAncestorOrSelfPages(getPage()->getID());

if (count($navigation) > 1) {
  $result = '';
  $i = 0;
  foreach($navigation as $entry) {
    $page = $entry['page'];

    if ($i < count($navigation) - 1) {
      $result .= "<a href=\"".Toolkit::pageURL($page->getID())."\">";
      $result .= $page->getProperty('title')."</a> &rarr;\n";
    }
    else {
      $result .= "<span class=\"currentPage\">".htmlspecialchars($page->getProperty('title'))."</span>\n";
    }

    $i++;
  }
  echo RenderingUtil::indent($result, 8);
}
?></div>

<div id="content">

<?php if(getTemplate()->isSectionDefined('right')): ?>
<p style="color: blue;">The right column is defined in this template.</p>
<?php endif; ?>
