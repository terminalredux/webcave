<h1><?= $title ?></h1>
<br><br>
<div class="article-list">
  <?php foreach ($articles as $article) : ?>
    <h3><a href="<?= URL ?>article/view/<?= $article->slug ?>"><?= $article->title ?></a></h3>
    <p><?= $article->available_from->format('d-m-Y') ?></p>
    <hr>
  <?php endforeach; ?>
</div>
