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
      <li>
        <a href="#"><i class="fa fa-envelope"></i> <span>Wiadomości</span><small class="label pull-right label-info">nowa!</small></a>
      </li>
      <li class="<?= $app->checkAction('article/add') ? 'active' : '' ?>">
        <a href="<?= URL ?>article/add"><i class="fa fa-pencil"></i> <span>Napisz artykuł</span></a>
      </li>
      <li class="treeview <?= $app->checkAction('article/list') ? 'active' : '' ?>">
        <a href="#">
          <i class="fa fa-file-text-o"></i> <span>Artykuły</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu <?= $app->checkAction('article/list') ? 'menu-open' : '' ?>" style="<?= $app->checkAction('article/list') ? 'display: block;' : '' ?>">

          <li><a href="<?= URL ?>article/list/publicated"><i class="fa fa-eye"></i> Publiczne</a></li>
          <li><a href="<?= URL ?>article/list/unpublicated"><i class="fa fa-eye-slash"></i> Niepubliczne</a></li>
          <li><a href="<?= URL ?>article/list/removed"><i class="fa fa-trash"></i> Usunięte</a></li>
          <li><a href="<?= URL ?>article/list/pending"><i class="fa fa-clock-o"></i> Oczekujące</a></li>
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
        $ulClass = 'menu-open';
        $ulStyle = 'display: block;';
      } else {
        $action = '';
        $ulClass = '';
        $ulStyle = '';
      }
      ?>
      <li class="treeview <?= $action ?>">
        <a href="#">
          <i class="fa fa-list-ul"></i> <span>Kategorie</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu <?= $ulClass ?>" style="<?= $ulStyle ?>">
          <li class="<?= $app->checkController('basecategory') ? 'active' : '' ?>">
            <a href="#"><i class="fa fa-star"></i> Kategorie główne <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu" <?= $app->checkAction('basecategory/list') ? 'menu-open' : '' ?> style="<?= $app->checkAction('basecategory/list') ? 'display: block;' : '' ?>">
              <li><a href="<?= URL ?>basecategory/list/public"><i class="fa fa-check"></i> Aktywne</a></li>
              <li><a href="<?= URL ?>basecategory/list/hidden"><i class="fa fa-eye-slash"></i> Ukryte</a></li>
              <li><a href="<?= URL ?>basecategory/list/removed"><i class="fa fa-trash"></i> Usunięte</a></li>
            </ul>
          </li>
          <li class="<?= $app->checkController('category') ? 'active' : '' ?>">
            <a href="#"><i class="fa fa-list-ul"></i> Podkategorie <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu <?= $app->checkAction('category/list') ? 'menu-open' : '' ?>" style="<?= $app->checkAction('category/list') ? 'display: block;' : '' ?>">
              <li><a href="<?= URL ?>category/list/active"><i class="fa fa-check"></i> Aktywne</a></li>
              <li><a href="<?= URL ?>category/list/hidden"><i class="fa fa-eye-slash"></i> Ukryte</a></li>
              <li><a href="<?= URL ?>category/list/removed"><i class="fa fa-trash"></i> Usunięte</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li class="<?= $app->checkController('tag') ? 'active' : '' ?>">
        <a href="<?= URL ?>tag/list"><i class="fa fa-hashtag"></i> <span>Tagi</span></a>
      </li>
      <li>
        <a href="#"><i class="fa fa-file-image-o"></i> <span>Zdjęcia</span></a>
      </li>
      <li>
        <a href="#"><i class="fa fa-pie-chart"></i> <span>Statystyki</span></a>
      </li>
      <li>
        <a href="#"><i class="fa fa-cogs"></i> <span>Ustawienia</span></a>
      </li>
      <li>
        <a href="#"><i class="fa fa-user-circle"></i> <span>Profil</span></a>
      </li>
      <li>
        <a href="#"><i class="fa fa-book"></i> <span>Instrukcja</span></a>
      </li>
    </ul>
  </section>

  <script>
    //$app->controllerAlias();
    $.sidebarMenu($('.sidebar-menu'))
  </script>
</div>
