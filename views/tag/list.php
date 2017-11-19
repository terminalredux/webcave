<h1>Tagi</h1>
<br>
<div class="row">
  <div class="col-md-12">
    <form method="post" action="<?= URL ?>tag/list" class="form-inline" id="tagForm">
      <div class="form-group">
        <input type="text" name="name" id="name" placeholder="Nazwa tagu" class="form-control">
        <button type="submit" class="btn btn-success">Dodaj</button>
      </div>
    </form>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-12">
    <?php foreach ($tags as $tag) : ?>
      <a href="#"
         role="button"
         data-toggle="tag-menu"
         data-content="
         <a href='<?= URL?>tag/remove/<?= $tag->id ?>' class='btn btn-danger change-status-btn' role='button'>Usuń</a>
         <a href='<?= URL?>tag/edit/<?= $tag->id ?>' class='btn btn-warning change-status-btn' role='button'>Zmień nazwę</a>"
         class="btn tag"><?= $tag->name ?></a>
    <?php endforeach; ?>
  </div>
</div>
<script>
$(document).ready(function(){
  $('[data-toggle="tag-menu"]').popover({
    html: 'true',
    title: 'Edycja tagu:',
    placement: 'left'
  });

  $('[data-toggle="tag-menu"]').on('click', function (e) {
    $('[data-toggle="tag-menu"]').not(this).popover('hide');
  });

  $('#tagForm').validate({
    rules: {
      name: {
        required: true,
        minlength: 1,
        maxlength: 100
      },
    }
  });
});
</script>
