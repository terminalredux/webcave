<h1>Edytuj kategorie</h1>
<br>
<div class="row">
  <div class="col-md-4">
    <form action="<?= URL ?>category/edit/<?= $category->id ?>" method="post" id="categoryForm">
      <div class="form-group">
        <input type="text"
               name="name"
               id="name"
               placeholder="Nazwa kategorii"
               value="<?= $category->name?>"
               class="form-control">
      </div>
      <button type="submit" class="btn btn-success">Dodaj</button>
    </form>
  </div>
</div>
<script>
  $('#categoryForm').validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
        maxlength: 100
      }
    }
  });
</script>
