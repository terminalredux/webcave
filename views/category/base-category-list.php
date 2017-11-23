<h1><?= $categories[0]->base_category->name ?></h1>
<br><br>
<?php foreach ($categories as $category) : ?>
  <h3><a href="<?= URL ?>article/category/<?= $category->slug ?>"><?= $category->name ?></a></h3>
  <hr>
<?php endforeach; ?>
