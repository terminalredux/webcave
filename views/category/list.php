<?php
use App\Models\Category\Category;
?>
<h1>Kategorie (<?= $title ?>)</h1>
<br>
<?= $this->render('category/form', ['baseCategories' => $baseCategories, 'editMode' => $editMode]) ?>
<br><br><br><br><br><br>
<div class="col-md-12">
  <table class="table table-striped table-hover" style="font-size: 0.9em;">
    <thead>
      <th>Nazwa</th>
      <th>Kategoria bazowa</th>
      <th>Utworzony</th>
      <th>Zmodyfikowany</th>
      <th>Status</th>
      <th></th>
    </thead>
    <tbody>
      <?php foreach ($categories as $category) : ?>
        <tr>
          <td><?= $category->name ?></td>
          <td><?= $category->base_category_id ?></td>
          <td><?= date_format($category->created_at,"Y/m/d H:i:s")  ?></td>
          <td><?= date_format($category->updated_at,"Y/m/d H:i:s") ?></td>
          <td class="text-center">
            <div data-toggle="status-menu"
                 class="<?= $category->setStatusColor() ?> article-status"
                 data-content="
                 <?php if ($category->isRemoved()) :?>
                   <a href='<?= URL?>category/changestatus/<?= $category->id ?>/active' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                   <a href='<?= URL?>category/changestatus/<?= $category->id ?>/hidden' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                 <?php elseif ($category->isActive()) : ?>
                   <a href='<?= URL?>category/changestatus/<?= $category->id ?>/hidden' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                 <?php elseif ($category->isHidden()) : ?>
                   <a href='<?= URL?>category/changestatus/<?= $category->id ?>/active' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                 <?php endif; ?>
                 ">
              <?= Category::getStatus()[$category->status] ?>
            </div>
          </td>
          <td>
            <a href="<?= URL ?>category/edit/<?= $category->id ?>" class="fa-icon">
              <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
            </a>
            <?php if ($category->isRemoved()) : ?>
              &nbsp;
              <a id="<?= $category->id ?>"
                 class="show-modal-btn"
                 data-toggle="modal"
                 data-target="#hard-remove-modal"
                 role="button"
                 class="fa-icon">
                <i class="fa fa-times" aria-hidden="true" title="Usuń z bazy danych"></i>
              </a>
            <?php else: ?>
              &nbsp;
              <a href="<?= URL ?>category/changestatus/<?= $category->id ?>/removed" class="fa-icon">
                <i class="fa fa-trash" aria-hidden="true" title="Usuń"></i>
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (empty($categories)) : ?>
    <div class="text-center">
      <h4>Brak danych</h4>
    </div>
  <?php endif; ?>
</div>
<!-- Modal -->
<div id="hard-remove-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Uwaga</h4>
      </div>
      <div class="modal-body">
        <p>Jesteś pewien że chcesz na stałe usunąć kategorię? Zmian nie będzie można przywrócić!</p>
      </div>
      <div class="modal-footer">
        <button href="<?= URL ?>category/delete/" class="btn btn-danger" id="hard-remove-btn">Usuń</button>
        <button class="btn btn-success" data-dismiss="modal">Anuluj</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  var id;

  $('.show-modal-btn').click(function () {
    id = $(this).attr('id');
  });

  $('#hard-remove-btn').click(function () {
    var url = $(this).attr('href');
    window.location = url + id;
  });

  $('[data-toggle="status-menu"]').popover({
    html: 'true',
    title: 'Zmień statusn na:',
    placement: 'left'
  });

  $('[data-toggle="status-menu"]').on('click', function (e) {
    $('[data-toggle="status-menu"]').not(this).popover('hide');
  });
});
</script>
