<?php
  use App\Models\BaseCategory\BaseCategory;
  $action = $editMode ? 'category/edit/' . $model->id : 'category/add';
?>
<?php if ($editMode) : ?>
  <h1>Edytuj kategorie</h1>
<?php endif; ?>
<br>
<div class="col-md-12">
  <div class="row">
    <div class="col-md-4">
      <form action="<?= URL . $action ?>" method="post" id="categoryForm">
        <div class="form-group">
          <input type="text"
                 name="name"
                 id="name"
                 placeholder="Nazwa kategorii"
                 class="form-control"
                 value="<?= $editMode ? $model->name : '' ?>">
        </div>
        <div class="form-group">
          <select class="form-control" id="base_category_id" name="base_category_id">
            <?php if (!$editMode) : ?>
              <option disabled selected value>Kategoria bazowa</option>
            <?php endif; ?>
            <?php foreach ($baseCategories as $baseCategory) : ?>
              <option value="<?= $baseCategory->id ?>" <?= $baseCategory->id == $model->base_category_id ? 'selected' : '' ?>>
                <?= $baseCategory->name ?>
                (<?= BaseCategory::getStatus()[$baseCategory->status] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-success"><?= $editMode ? 'Edytuj' : 'Dodaj' ?></button>
      </form>
    </div>
  </div>
</div>
