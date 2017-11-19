<h1>Artykuły ()</h1>
<br>
<small>Sortowanie: data zmodyfikowania</small>
<br><br>
<div class="row">
  <div class="col-md-12">
    <table class="table table-striped table-hover" style="font-size: 0.9em;">
      <thead>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Tytuł</th>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Kategoria</th>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Data publikacji</th>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Utworzony</th>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Zmodyfikowany</th>
        <th class="text-center">
          <i class="fa fa-eye" aria-hidden="true" title="Liczba wyświetleń przez gości"></i>
        </th>
        <th><i class="fa fa-sort" aria-hidden="true"></i> Status</th>
        <th></th>
      </thead>
      <tbody>
        <?php foreach ($articles as $article) : ?>
          <tr>
            <td title="<?= $article->title ?>"><?= $article->shortTitle() ?></td>
            <td class="text-center">
              <div class="<?= $article->category->getCategoryClass() ?>">
                <?= $article->category->name ?>
              </div>
            </td>
            <td class="text-center">
              <div class="<?= $article->isPending() ? 'model-pending' : '' ?>" title="<?= $article->isPending() ? 'Dopiero oczekuje na publikacje' : '' ?>">
                <?= $article->available_from ? $article->available_from->format('d/m/Y H:i') : '-' ?>
              </div>
            </td>
            <td><?= $article->created_at->format('d/m/Y H:i') ?></td>
            <td class="text-center">
              <div title="<?= !empty($article->content_edited) ? 'Treść edytowano: ' . $article->content_edited->format('d-m-Y H:i') : '' ?>"
                   class="<?= !empty($article->content_edited) ? 'model-edited' : '' ?>">
                <?= $article->isEdited() ? $article->updated_at->format('d/m/Y H:i') : '-' ?>
              </div>
            </td>
            <td class="text-center"><?= $article->views ?></td>
            <td class="text-center">
              <div class="<?= $article->getStatusClass() ?> article-status"
                   data-toggle="status-menu"
                   data-content="
                   <?php if ($article->isRemoved() || $article->isSketch()) :?>
                     <a href='<?= URL?>article/changestatus/<?= $article->id ?>/publicated' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                     <a href='<?= URL?>article/changestatus/<?= $article->id ?>/unpublicated' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                   <?php elseif ($article->isPublicated()) : ?>
                     <a href='<?= URL?>article/changestatus/<?= $article->id ?>/unpublicated' class='btn btn-warning change-status-btn' role='button'>Ukryty</a>
                   <?php elseif ($article->isUnpublicated()) : ?>
                     <a href='<?= URL?>article/changestatus/<?= $article->id ?>/publicated' class='btn btn-success change-status-btn' role='button'>Aktywny</a>
                   <?php endif; ?>
                   ">
                <?= $article->getStatusName() ?>
              </div>
            </td>
            <td>
              <a href="<?= URL ?>article/view/<?= $article->slug ?>" class="fa-icon">
                <i class="fa fa-eye" aria-hidden="true" title="Podgląd"></i>
              </a>
              &nbsp;
              <a href="<?= URL ?>article/edit/<?= $article->id ?>" class="fa-icon">
                <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
              </a>
              <?php if ($article->isRemoved() || $article->isSketch()) : ?>
                &nbsp;
                <a id="<?= $article->id ?>"
                   class="show-modal-btn"
                   role="button"
                   data-toggle="modal"
                   data-target="#hard-remove-modal"
                   class="fa-icon">
                  <i class="fa fa-times" aria-hidden="true" title="Usuń z bazy danych"></i>
                </a>
              <?php else: ?>
                &nbsp;
                <a href="<?= URL ?>article/changestatus/<?= $article->id ?>/removed" class="fa-icon">
                  <i class="fa fa-trash" aria-hidden="true" title="Przenieś do usuniętych"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($articles)) : ?>
      <div class="text-center">
        <h4>Brak danych</h4>
      </div>
    <?php endif; ?>
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
        <p>Jesteś pewien że chcesz na stałe usunąć artykuł? Zmian nie będzie można przywrócić!</p>
      </div>
      <div class="modal-footer">
        <button href="<?= URL ?>article/delete/" class="btn btn-danger" id="hard-remove-btn">Usuń</button>
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
