<h1>Créer un compte</h1>
<form method="POST" action="validation_creerCompte">
  <div class="mb-3">
    <label for="login" class="form-label">Login</label>
    <input type="text" class="form-control" name="login" id="login" required>
  </div>
  <div class="mb-3">
    <label for="mail" class="form-label">Email</label>
    <input type="mail" class="form-control" id="mail" name="mail" required>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password" required>
  </div>
  <button type="submit" class="btn btn-primary">Créer !</button>
</form>