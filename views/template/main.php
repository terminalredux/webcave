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
        <!--
        <li><a href="<?= URL ?>article"><i class="fa fa-file-text-o" aria-hidden="true"></i> Artykuły</a></li>
        -->

      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li>
          <form action="<?= URL ?>site/search" method="get">
            <div class="input-group" style="width: 400px; margin-top: 7px;">
              <input type="text" class="form-control" id="txt" name="txt">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Szukaj</button>
              </span>
            </div>
        </form>
        </li>
        <li><a href="<?= URL ?>site/login">Zaloguj <i class="fa fa-user-circle-o" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </nav>
  <div class="container">
    <?php
      $app->getContent();
    ?>
</div>
</div>
