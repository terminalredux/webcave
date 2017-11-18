<div class="row">
  <h1><?= $article->title ?></h1>
  <a href="#" role="button" class="btn category"><?= $article->category->name ?></a>
  <hr>
  <?= $article->content ?>
</div>
