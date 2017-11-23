<h1>Kategorie</h1>
<br>
<ul>
  <?php foreach ($list as $baseCategory) : ?>
    <?php if ($baseCategory->isActive()) : ?>
    <li><?= $baseCategory->name ?>
      <?php if ($baseCategory->categories) : ?>
        <ul>
          <?php foreach ($baseCategory->categories as $category) : ?>
            <?php if (count($category->articles) && $category->isActive()) : ?>
            <li><a href="<?= URL ?>article/category/<?= $category->slug ?>"><?= $category->name ?> (<?= count($category->articles) ?>)</a></li>
          <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endif; ?>
  <?php endforeach; ?>
</ul>
