<?php
  use Libs\Base\Bootstrap;
  use Libs\FlashMessage\Flash;
  $app = Bootstrap::getInstance();
?>
<div class="admin_template">
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?= URL ?>dashboard">WebCaveAdmin</a>
      </div>
      <ul class="nav navbar-nav">

      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?= URL ?>site/logout">Wyloguj <i class="fa fa-power-off" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </nav>
    <div id="admin-panel-content">
      <?= Flash::showMessage() ?>
      <?php
        $app->getContent();
      ?>
    </div>
    <section style="width: 250px; pos">
    <ul class="sidebar-menu">
      <li class="header">PANEL ADMINISTRACYJNY</li>
      <li class="<?= $app->checkAction('article/add') ? 'active' : '' ?>">
        <a href="<?= URL ?>article/add"><i class="fa fa-pencil"></i> <span>Napisz artykuł</span></a>
      </li>
      <li class="treeview <?= $app->checkAction('article/list') ? 'active' : '' ?>">
        <a href="#">
          <i class="fa fa-file-text-o"></i> <span>Artykuły</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="<?= URL ?>article/list/active"><i class="fa fa-check"></i> Aktywne</a></li>
          <li><a href="<?= URL ?>article/list/publicated"><i class="fa fa-eye"></i> Publiczne</a></li>
          <li><a href="<?= URL ?>article/list/notpublicated"><i class="fa fa-eye-slash"></i> Niepubliczne</a></li>
          <li><a href="<?= URL ?>article/list/removed"><i class="fa fa-trash"></i> Usunięte</a></li>
          <li><a href="#"><i class="fa fa-clock-o"></i> Oczekujące</a></li>
          <li><a href="<?= URL ?>article/list/sketch"><i class="fa fa-sticky-note-o"></i> Szkice</a></li>
        </ul>
      </li>
      <li class="treeview <?= $app->checkAction('comment/list') ? 'active' : '' ?>">
        <a href="#">
          <i class="fa fa-comments-o"></i> <span>Komentarze</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="<?= URL ?>comment/list/not-publicated"><i class="fa fa-check"></i> Do akceptacji</a></li>
          <li><a href="<?= URL ?>comment/list/removed"><i class="fa fa-check"></i> Usunięte</a></li>
        </ul>
      </li>
      <?php
      if ($app->checkController('category') || $app->checkController('basecategory')) {
        $action = 'active';
      } else {
        $action = '';
      }
      ?>
      <li class="treeview <?= $action ?>">
        <a href="#">
          <i class="fa fa-list-ul"></i> <span>Kategorie</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="<?= URL ?>basecategory/index"><i class="fa fa-star"></i> Kategorie bazowe</a></li>
          <li><a href="<?= URL ?>category/list/active"><i class="fa fa-check"></i> Aktywne</a></li>
          <li><a href="<?= URL ?>category/list/removed"><i class="fa fa-trash"></i> Usunięte</a></li>
        </ul>
      </li>
      <li class="<?= $app->checkController('file') ? 'active' : '' ?>">
        <a href="<?= URL ?>file/index"><i class="fa fa-file-image-o"></i> <span>Pliki</span></a>
      </li>
    </ul>
  </section>

  <script>
    //$app->controllerAlias();
    $.sidebarMenu($('.sidebar-menu'))
  </script>
</div>
