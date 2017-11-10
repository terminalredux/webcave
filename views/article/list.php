<?php
use App\Components\Helpers\ArticleHelper;
use App\Models\Category\CategoryQuery;
use App\Models\Article;
?>
<?php foreach ($data as $article) : ?>
  <?= $article['category_name'] ?><br>
<?php endforeach; ?>

<h1>Artykuły</h1>
<small>Sortowanie według ustalonej daty publikacji (malejąco)</small>
<br></br>
<div class="row">
  <div class="col-md-12">
    <table class="table table-striped table-hover" style="font-size: 0.9em;">
      <thead>
        <th>Tytuł</th>
        <th>Kategoria</th>
        <th>User ID</th>
        <th>Data publikacji</th>
        <th>Utworzony</th>
        <th>Zmodyfikowany</th>
        <th>Status</th>
        <th></th>
      </thead>
      <tbody>
        <?php foreach ($articles as $article) : ?>
          <tr>
            <td title="<?= $article->title ?>">
                <?= $article->shortTitle() ?>
            </td>
            <td><?= CategoryQuery::getByID($article->category_id)->name ?></td>
            <td><?= $article->user_id ?></td>
            <td class="text-center">
              <div title="<?= $formatter->dateTime($article->available_from, true) ?>"
                   class="<?= $article->isPending() ? 'date-pending' : '' ?>">
                <?= $formatter->dateTime($article->available_from) ?>
              </div>
            </td>
            <td title="<?= $formatter->dateTime($article->created_at, true) ?>">
              <?= $formatter->dateTime($article->created_at) ?>
            </td>
            <td class="text-center" title="">
              <div class="<?= $article->isEdited() ? 'model-edited' : ''?>"
                   title="<?= $article->isEdited() ? 'Artykuł został edytowany: ' .
                    $formatter->dateTime($article->updated_at, true) : ''?>">
                <?= $article->isEdited() ? $formatter->dateTime($article->updated_at) : '-' ?>
              </div>
            </td>
            <td class="text-center">
              <div data-toggle="status-menu"
                   class="<?= $article->statusClass() ?> article-status"
                   data-content="
                     <?php if ($article->status == ArticleHelper::PUBLICATED) : ?>
                       <a href='<?= URL?>article/changestatus/notpublic/<?= $article->id ?>' class='btn btn-warning change-status-btn' role='button'>Niepubliczny</a>
                     <?php elseif ($article->status == ArticleHelper::NOT_PUBLICATED) : ?>
                       <a href='<?= URL?>article/changestatus/public/<?= $article->id ?>' class='btn btn-success change-status-btn' role='button'>Publiczny</a>
                     <?php elseif ($article->status == ArticleHelper::REMOVED || $article->status == ArticleHelper::SKETCH) : ?>
                       <a href='<?= URL?>article/changestatus/notpublic/<?= $article->id ?>' class='btn btn-warning change-status-btn' role='button'>Niepubliczny</a><br>
                       <a href='<?= URL?>article/changestatus/public/<?= $article->id ?>' class='btn btn-success change-status-btn' role='button'>Publiczny</a>
                     <?php endif; ?>
                   ">
                <?= ArticleHelper::getStatus()[$article->status] ?>
              </div>
            </td>
            <td>
              <a href="<?= URL ?>article/view/<?= $article->slug ?>" class="fa-icon">
                <i class="fa fa-eye" aria-hidden="true" title="Podgląd"></i>
              </a>
              &nbsp;
              <a href="#" class="fa-icon">
                <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
              </a>
              <?php if ($article->isRemoved() || $article->status == ArticleHelper::SKETCH) : ?>
                &nbsp;
                <a id="<?= $article->id ?>"
                   class="show-modal-btn"
                   role="button"
                   data-toggle="modal"
                   data-target="#remove-article-modal"
                   class="fa-icon">
                  <i class="fa fa-times" aria-hidden="true" title="Usuń z bazy danych"></i>
                </a>
              <?php else: ?>
                &nbsp;
                <a href="<?= URL ?>article/changestatus/removed/<?= $article->id ?>" class="fa-icon">
                  <i class="fa fa-trash" aria-hidden="true" title="Przenieś do usuniętych"></i>
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
<div id="remove-article-modal" class="modal fade" role="dialog">
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
        <button href="<?= URL ?>article/remove/" class="btn btn-danger" id="remove-article-btn">Usuń</button>
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

  $('#remove-article-btn').click(function () {
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
