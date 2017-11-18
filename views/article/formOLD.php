<?php
use App\Components\Helpers\ArticleHelper;

if ($editMode) {
  $action = "article/edit/$article->id";
} else {
  $action = "article/add";
}
?>
<form action="<?= URL . $action ?>" method="post" id="articleForm">
  <h1><?= $editMode ? 'Edycja artykułu' : 'Dodaj artykuł' ?></h1>
  <br>
  <?php if (!$editMode) :?>
    <div class="row text-right">
      <div class="checkbox">
        <label for="is_sketch"><input type="checkbox" value="1" name="is_sketch" id="is_sketch">Zapisz jako szkic</label>
      </div>
    </div>
  <?php endif; ?>
  <div class="row">
    <div class="form-group">
      <label for="title">Tytuł</label>
      <input type="text" name="title" id="title" class="form-control" value="<?= $editMode ? $article->title : '' ?>">
    </div>
  </div>
  <div class="row">
    <div class="col-md-6" style="padding-left: 0;">
      <div class="form-group">
        <label for="category_id">Kategoria</label>
        <select class="form-control" id="category_id" name="category_id">
          <?php if (!$editMode) : ?>
            <option disabled selected value>Wybierz kategorie</option>
          <?php endif; ?>
          <?php foreach ($categories as $category) : ?>
            <?php if ($editMode && $category->id == $article->category_id) : ?>
              <option value="<?= $category->id ?>" selected="selected"><?= $category->name ?></option>
            <?php else : ?>
              <option value="<?= $category->id ?>"><?= $category->name ?></option>
            <?php endif; ?>

          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
    <?php if ($editMode && $article->status == ArticleHelper::SKETCH) : ?>
    <?php else: ?>
      <div class="row" id="available-form-row">
        <div class="col-md-3" style="padding-left: 0">
          <button class="btn btn-info" id="btn-set-pub-date" style="width: 100%">Ustaw date publikacji</button>
        </div>
        <div class="col-md-3">
          <div id="setting-pub-date">
            <div class="form-group" style="margin-bottom: 0;">
              <div class='input-group date' id='available-from-datetimepicker'>
                  <input type="text"
                         name="available_from"
                         id="available_from"
                         class="form-control"
                         value="<?= $editMode ? date('d-m-Y H:i', $article->available_from) : '' ?>"
                         placeholder="Zmieniona data dodania wpisu">
                  <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <br>
  <div class="row">
    <div class="form-group">
      <label for="content">Treść</label>
      <textarea id="content" name="content" class="form-control"><?= $editMode ? $article->content : '' ?></textarea>
    </div>
  </div>
  <div class="row" style="margin-bottom: 30px;">
    <button type="submit" class="btn btn-success"><?= $editMode ? 'Edytuj' : 'Dodaj' ?></button>
  </div>
</form>

<!-- Froala Editor -->
<script type="text/javascript" src="<?= URL ?>vendor\bower\codemirror\lib\codemirror.js"></script>
<script type="text/javascript" src="<?= URL ?>vendor\bower\codemirror\mode\xml\xml.js"></script>
<script type="text/javascript" src="<?= URL ?>vendor\bower\froala-wysiwyg-editor\js\froala_editor.pkgd.min.js"></script>
<script>
$(function() { $('textarea').froalaEditor({
  tabSpaces: 4,
  })
});
</script>
<script>
$('document').ready(function(){
  //available-form-row
  //var isSketch = $('input[name=is_sketch]:checked');
  $('#is_sketch').val($(this).is(':checked'));

    $('#is_sketch').change(function() {
        $('#available-form-row').fadeToggle();
    });


  $('#setting-pub-date').hide();
  $("#btn-set-pub-date").click(function( event ) {
    event.preventDefault();
    $('#setting-pub-date').slideToggle();
  });

  $(function () {
    $('#available-from-datetimepicker').datetimepicker({
      format: 'D-M-YYYY H:mm'
    });
  });

  $('#articleForm').validate({
    rules: {
      title: {
        required: true,
        minlength: 10,
        maxlength: 255
      },
      content: {
        required: true
      },
      category_id: {
        required: true,
        digits: true
      }
    }
  });

});
</script>
