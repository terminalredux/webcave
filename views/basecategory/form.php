<?php
  $action = $editMode ? 'basecategory/edit/' . $model->id : 'basecategory/add';
?>
<?php if ($editMode) : ?>
  <h1>Edytuj kategorie bazową</h1>
<?php endif; ?>
<br>
<div class="row">
  <div class="col-md-4">
    <form action="<?= URL . $action ?>" method="post" id="baseCategoryForm">
      <div class="form-group">
        <input type="text"
               name="name"
               id="name"
               placeholder="Nazwa kategorii bazowej"
               class="form-control"
               value="<?= $editMode ? $model->name : '' ?>">
      </div>
      <button type="submit" class="btn btn-success"><?= $editMode ? 'Edytuj' : 'Dodaj' ?></button>
    </form>
  </div>
</div>
