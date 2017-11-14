<h1>Komentarze</h1>
<br>
<br>
<?php foreach ($comments as $comment) : ?>
  <h5><strong><?= $comment->nick ?>(<?= $comment->ip ?>)</strong> w <?= $comment->article_title ?>(<?= $comment->article_category_id ?>) o <?= $formatter->dateTime($comment->created_at)?></h5>
  <p><?= $comment->content ?></p>
  <a href="#" role="button" class="btn btn-info">Pełny kontekst</a>
  <a href="#" role="button" class="btn btn-success">Akceptuj</a>
  <a href="#" role="button" class="btn btn-danger">Usuń</a>
  <hr>
<?php endforeach; ?>
