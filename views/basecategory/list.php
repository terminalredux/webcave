<?php
use App\Models\BaseCategory\BaseCategory;
?>
<h1>Kategorie bazowe (<?= $title ?>)</h1>
<?= $this->render('basecategory/form', ['editMode' => $editMode]); ?>
<br><br><br><br><br><br>
<div class="col-md-12">
  <table class="table table-striped table-hover" style="font-size: 0.9em;">
    <thead>
      <th>Nazwa</th>
      <th>l. kategorii</th>
      <th>Utworzony</th>
      <th>Zmodyfikowany</th>
      <th>Status</th>
      <th></th>
    </thead>
    <tbody>
      <?php foreach ($list as $model) : ?>
        <tr>
          <td><?= $model->name ?></td>
          <td><?= count($model->categories) ?></td>
          <td><?= date_format($model->created_at,"Y/m/d H:i:s")  ?></td>
          <td><?= date_format($model->updated_at,"Y/m/d H:i:s") ?></td>
          <td class="text-center">
            <div data-toggle="status-menu"
                 class="<?= $model->setStatusColor() ?> article-status"
                 data-content="
                 <?php if ($model->isRemoved()) :?>
                   <a href='<?= URL?>basecategory/changestatus/<?= $model->id ?>/active' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                   <a href='<?= URL?>basecategory/changestatus/<?= $model->id ?>/hidden' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                 <?php elseif ($model->isActive()) : ?>
                   <a href='<?= URL?>basecategory/changestatus/<?= $model->id ?>/hidden' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                 <?php elseif ($model->isHidden()) : ?>
                   <a href='<?= URL?>basecategory/changestatus/<?= $model->id ?>/active' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                 <?php endif; ?>
                 ">
              <?= BaseCategory::getStatus()[$model->status] ?>
            </div>
          </td>
          <td>
            <a href="<?= URL ?>basecategory/edit/<?= $model->id ?>" class="fa-icon">
              <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
            </a>
            <?php if ($model->isRemoved()) : ?>
              &nbsp;
              <a id="<?= $model->id ?>"
                 class="show-modal-btn"
                 data-toggle="modal"
                 data-target="#hard-remove-modal"
                 role="button"
                 class="fa-icon">
                <i class="fa fa-times" aria-hidden="true" title="Usuń z bazy danych"></i>
              </a>
            <?php else: ?>
              &nbsp;
              <a href="<?= URL ?>basecategory/changestatus/<?= $model->id ?>/removed" class="fa-icon">
                <i class="fa fa-trash" aria-hidden="true" title="Usuń"></i>
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (empty($list)) : ?>
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
        <p>Jesteś pewien że chcesz na stałe usunąć kategorię bazową? Zmian nie będzie można przywrócić!</p>
      </div>
      <div class="modal-footer">
        <button href="<?= URL ?>basecategory/delete/" class="btn btn-danger" id="hard-remove-btn">Usuń</button>
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
