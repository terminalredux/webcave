<?php
  use Libs\Base\Bootstrap;
  $app = Bootstrap::getInstance();
?>
<div class="main_template">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?= URL ?>site">WebCave</a>
      </div>
      <ul class="nav navbar-nav">
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?= URL ?>category/all">Kategorie <i class="fa fa-book" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </nav>
  <div class="container">
    <?php
      $app->getContent();
    ?>
</div>
</div>
