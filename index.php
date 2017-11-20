<!doctype html>
<?php
  require_once "init.php";
  use Libs\Base\Bootstrap;
  $app = Bootstrap::getInstance();
  $app->sessionInit();
?>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>webcave.pl</title>
  <meta name="description" content="WebCave">
  <meta name="Woland" content="WebCave">

  <link rel="stylesheet" href="<?= URL ?>vendor/bower/bootstrap/dist/css/bootstrap.min.css">
  <script src="<?= URL ?>vendor/bower/jquery/dist/jquery.min.js"></script>
  <!--
  <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
  -->
	<script src="<?= URL ?>vendor/bower/bootstrap/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="<?= URL ?>vendor/bower/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= URL ?>web/css/main.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link rel='shortcut icon' type='image/x-icon' href='http://www.iconninja.com/files/75/658/333/square-development-programming-code-brackets-coding-website-icon.svg' />
  <!-- Admin's left menu -->
  <link rel="stylesheet" href="<?= URL ?>web/left-menu/dist/sidebar-menu.css">
  <script src="<?= URL ?>web/left-menu/dist/sidebar-menu.js"></script>
  <!-- Syntax highlighter: highlightjs -->
  <link rel="stylesheet" href="<?= URL ?>vendor/bower/highlightjs/styles/dracula.css">
  <script src="<?= URL ?>vendor/bower/highlightjs/highlight.pack.min.js"></script>
  <script>hljs.initHighlightingOnLoad();</script>
  <!-- Jquery-Validation -->
  <script src="<?= URL ?>vendor/bower/jquery-validation/dist/jquery.validate.min.js"></script>
  <script src="<?= URL ?>web/js/jquery-validate-translations-pl.js"></script>
  <!-- Bootstrap DateTimePicker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
  <!-- Froala WYSIWYG Editor -->
  <link rel="stylesheet" href="<?= URL ?>vendor\bower\codemirror\lib\codemirror.css">
  <link href="<?= URL ?>vendor\bower\froala-wysiwyg-editor\css\froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= URL ?>vendor\bower\froala-wysiwyg-editor\css\froala_style.min.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="<?= URL ?>vendor\bower\froala-wysiwyg-editor\js\plugins\file.min.js"></script>

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>

<body>
  <?php
    $app->initTemplate();
  ?>
</body>
</html>
