<!DOCTYPE HTML>
<html>
<head>
  <title><?php echo htmlspecialchars($title); ?></title>

  <?php \Pages\PageRenderer::includeStylesheets(); ?>
  <?php \Pages\PageRenderer::includeJavascripts(); ?>
</head>
<body>

<?php \Pages\PageRenderer::requireTemplate("navigation"); ?>

<div class="content">

