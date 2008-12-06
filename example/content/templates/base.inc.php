<?php if (is_null(getTemplate())) die(); ?>
<?php getTemplate()->includeSection('header'); ?>
<?php getTemplate()->includeSection('body'); ?>
<?php getTemplate()->includeSection('footer'); ?>