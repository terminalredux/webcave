<h1>Dodaj plik</h1>
<form action="<?= URL ?>file/add" method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="form-group">
      <label for="file">Plik</label>
      <input type="file" name="file" id="file" class="form-control">
    </div>
  </div>
  <div class="row">
    <button type="submit" class="btn btn-success">Dodaj</button>
  </div>
</form>
