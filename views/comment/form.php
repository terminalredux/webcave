<h4>Dodaj komentarz</h4>
<div class="row">
  <div class="col-md-6">
  <form action="<?= URL ?>comment/add" method="POST" id="commenteForm">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="nick">Nick</label>
            <input type="text" name="nick" id="nick" class="form-control">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email" class="form-control">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="content">Komentarz</label>
            <textarea id="content" name="content" class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-success">Dodaj</button>
        </div>
      </div>
  </form>
  </div>
  <div class="col-md-6">
  </div>
</div>
