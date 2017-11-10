<h1>Logowanie</h1>
<div class="row">
  <div class="col-md-6">
    <form action="<?= URL ?>site/login" method="post">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="password">Hasło</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-default">Zaloguj</button>
        </div>
      </div>
    </form>
  </div>
</div>
