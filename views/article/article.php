<h1><?= $article->title?></h1>
<hr>
<?= $article->content ?>
<br>
<br>
<?= $article->user->alias ?>
<br>
<hr>
<br>
<?= $this->render('comment/form') ?>
<br>
<br>
<?= $this->render('comment/public-list') ?>
