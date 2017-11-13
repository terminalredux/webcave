<?php
use App\Components\Helpers\{
  ArticleHelper,
  ArticleTableHelper as Helper
};
use App\Models\Category\CategoryQuery;
use App\Models\Article;
?>

<h1>Artykuły</h1>
<br></br>
<div class="row">
  <div class="col-md-12">
    <table class="table table-striped table-hover" style="font-size: 0.9em;">
      <thead>
        <th>Tytuł</th>
        <th>Kategoria</th>
        <th>Data publikacji</th>
        <th>Utworzony</th>
        <th>Zmodyfikowany</th>
        <th class="text-center">
          <i class="fa fa-eye" aria-hidden="true" title="Liczba wyświetleń przez gości"></i>
        </th>
        <th>Status</th>
        <th></th>
      </thead>
      <tbody>
        <?php foreach ($tableRows as $article) : ?>
          <?php
            $isPending = Helper::isPending($article->available_from);
            $isUpdated = Helper::isUpdated($article->created_at, $article->updated_at);
            $isRemoved = Helper::isRemoved($article->status);
            $isPublicated = Helper::isPublicated($article->status);
            $isNotPublicated = Helper::isNotPublicated($article->status);
            $isSketch = Helper::isSketch($article->status);
          ?>
          <tr>
            <td title="<?= $article->title ?>">
                <?= Helper::shortTitle($article->title) ?>
            </td>
            <td><?= $article->category_name ?></td>
            <td class="text-center">
              <div title="<?= $formatter->dateTime($article->available_from, true) ?>"
                   class="<?= $isPending ? 'date-pending' : '' ?>">
                <?= $isSketch ? '-' : $formatter->dateTime($article->available_from) ?>
              </div>
            </td>
            <td title="<?= $formatter->dateTime($article->created_at, true) ?>">
              <?= $formatter->dateTime($article->created_at) ?>
            </td>
            <td class="text-center" title="">
              <div class="<?= !$isSketch && $isUpdated ? 'model-edited' : '' ?>"
                   title="<?= $isUpdated ? 'Artykuł został edytowany: ' .
                    $formatter->dateTime($article->updated_at, true) : '' ?>">
                <?= $isUpdated ? $formatter->dateTime($article->updated_at) : '-' ?>
              </div>
            </td>
            <td class="text-center" title="Liczba wyświetleń przez gości">
              <?= !$isSketch ? $article->views : '-' ?>
            </td>
            <td class="text-center">
              <div data-toggle="status-menu"
                   class="<?= Helper::statusClass($article->status) ?> article-status"
                   data-content="
                     <?php if ($isPublicated) : ?>
                       <a href='<?= URL?>article/changestatus/notpublic/<?= $article->id ?>' class='btn btn-warning change-status-btn' role='button'>Niepubliczny</a>
                     <?php elseif ($isNotPublicated) : ?>
                       <a href='<?= URL?>article/changestatus/public/<?= $article->id ?>' class='btn btn-success change-status-btn' role='button'>Publiczny</a>
                     <?php elseif ($isRemoved || $isSketch) : ?>
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
              <a href="<?= URL ?>article/edit/<?= $article->id ?>" class="fa-icon">
                <i class="fa fa-pencil" aria-hidden="true" title="Edytuj"></i>
              </a>
              <?php if ($isRemoved || $isSketch) : ?>
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
    <?php if (empty($tableRows)):  ?>
      <div class="row text-center">
        <h4>Brak danych</h4>
      </div>
    <?php endif; ?>
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
