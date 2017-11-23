<div class="row">
  <h1><?= $article->title ?></h1>
  <br>
  <a href="<?= URL ?>category/main/<?= $article->category->base_category->slug ?>" role="button" class="btn base-category"><?= $article->category->base_category->name ?></a>
  <a href="<?= URL ?>article/category/<?= $article->category->slug ?>" role="button" class="btn category"><?= $article->category->name ?></a>
  <hr>
  <?= $article->content ?>
</div>
