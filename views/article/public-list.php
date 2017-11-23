<h1><?= $title ?></h1>
<br><br>
<div class="article-list">
  <?php foreach ($articles as $article) : ?>
    <div class="row">
      <div class="col-md-6 text-left">
        <small>
          <a href="<?= URL ?>category/main/<?= $article->category->base_category->slug ?>">
            <?= $article->category->base_category->name ?>
          </a> -
          <a href="<?= URL ?>article/category/<?= $article->category->slug ?>">
            <?= $article->category->name ?>
          </a>
        </small>
      </div>
      <div class="col-md-6 text-right">
        <small><?= $article->available_from->format('d-m-Y H:i') ?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h3><a href="<?= URL ?>article/view/<?= $article->slug ?>"><?= $article->title ?></a></h3>
      </div>
    </div>


    <hr>
  <?php endforeach; ?>
</div>
