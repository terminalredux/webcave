<div class="row">
  <h1><?= $article->title ?></h1>
  <br>
  <a href="#" role="button" class="btn base-category"><?= $article->category->base_category->name ?></a>
  <a href="#" role="button" class="btn category"><?= $article->category->name ?></a>
  <hr>
  <?= $article->content ?>
</div>
