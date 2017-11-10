<?php
use App\Components\Helpers\CategoryHelper;
use Libs\Base\Bootstrap;
$app = Bootstrap::getInstance();
?>
<h1>Kategorie</h1>
<br>
<?php if (!$app->checkParam('removed')) : ?>
  <div class="row">
    <div class="col-md-4">
      <form action="<?= URL ?>category/list" method="post" id="categoryForm">
        <div class="form-group">
          <input type="text" name="name" id="name" placeholder="Nazwa kategorii" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Dodaj</button>
      </form>
    </div>
  </div>
  <br>
<?php endif; ?>
<small>Sortowanie według daty zmodyfikowania (malejąco)</small>
<br><br>
<div class="row">
  <div class="col-md-12">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Kategoria</th>
          <th>Utworzono</th>
          <th>Zmodyfikowano</th>
          <th>Status</th>
          <th>Zmień status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($categories as $category): ?>
          <tr>
            <td><?= $category->name ?></td>
            <td><?= date('d/m/Y H:i', $category->created_at) ?></td>
            <td><?= date('d/m/Y H:i', $category->updated_at) ?></td>
            <td  class="text-center">
              <div class="<?= CategoryHelper::bgColor()[$category->status] ?> ">
                <?= CategoryHelper::getStatus()[$category->status] ?>
              </div>
            </td>
            <td>
              <?php if ($category->status == CategoryHelper::STATUS_ACTIVE) : ?>
                <a href="<?= URL ?>category/hide/<?= $category->id ?>">Ukryj</a>
              <?php elseif ($category->status == (CategoryHelper::STATUS_HIDDEN || CategoryHelper::STATUS_REMOVED)) : ?>
                <a href="<?= URL ?>category/activation/<?= $category->id ?>">Przywróć</a>
              <?php endif; ?>
            </td>
            <td>
              <a href="<?= URL ?>category/edit/<?= $category->id ?>" class="fa-icon">
                <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
              </a>
              <?php if ($category->status != CategoryHelper::STATUS_REMOVED) : ?>
                &nbsp;
                <a href="<?= URL ?>category/softremove/<?= $category->id ?>" class="fa-icon">
                  <i class="fa fa-trash" aria-hidden="true" title="Usuń"></i>
                </a>
              <?php endif; ?>
              <?php if ($category->status == CategoryHelper::STATUS_REMOVED) : ?>
                &nbsp;
                <a id="<?= $category->id ?>" class="show-modal-btn" role="button" data-toggle="modal" data-target="#hard-remove-modal" class="fa-icon">
                  <i class="fa fa-times" aria-hidden="true" title="Twarde kasowanie"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
</div>
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
        <button href="<?= URL ?>category/hardremove/" class="btn btn-danger" id="hard-remove-btn">Usuń</button>
        <button class="btn btn-success" data-dismiss="modal">Anuluj</button>
      </div>
    </div>
  </div>
</div>

<script>
  var id;

  $('.show-modal-btn').click(function () {
    id = $(this).attr('id');
  });

  $('#hard-remove-btn').click(function () {
    var url = $(this).attr('href');
    window.location = url + id;
  });

  $('#categoryForm').validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
        maxlength: 100
      }
    }
  });
</script>
