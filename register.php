<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';

$register = register($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Daftar</title>
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <style>
        body {
    background-color: #2C3E50; /* Navy Blue */
    color: #4A4A4A; /* Dark Grey */
}

.card {
    background-color: #FDEBD0; /* Beige Muda */
    border: none;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #FF9F9F; /* Peach Soft */
    color: white;
    font-weight: bold;
}

.button {
    background-color: #FF9F9F; /* Peach Soft */
    color: white;
    width: 100%;
    transition: background-color 0.3s ease;
}

.button:hover {
    background-color: #e68f8f; /* Versi lebih gelap dari peach */
}

.form-control {
    border: 1px solid #FF9F9F; /* Peach Soft */
    border-radius: 5px;
}

.form-control:focus {
    border-color: #FF9F9F;
    box-shadow: 0 0 5px rgba(255, 159, 159, 0.5);
}

.form-check-input {
    border-color: #FF9F9F;
}

.form-check-label {
    color: #4A4A4A;
}

a {
    color: #FF9F9F;
    text-decoration: none;
}

a:hover {
    color: #e68f8f;
    text-decoration: underline;
}

    </style>
</head>

<body class="bg-light">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-7">
              <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                  <h3 class="text-center font-weight-light my-4">Daftar</h3>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-floating mb-3">
                      <input class="form-control <?php echo (!empty($register['nama_err'])) ? 'is-invalid' : ''; ?>" id="inputNama" type="text" name="nama" placeholder="Nama" value="<?php echo $register['nama']; ?>" />
                      <label for="inputNama">Nama</label>
                      <span class="invalid-feedback"><?php echo $register['nama_err']; ?></span>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control <?php echo (!empty($register['username_err'])) ? 'is-invalid' : ''; ?>" id="inputUsername" name="username" placeholder="Username" value="<?php echo $register['username']; ?>" />
                      <label for="inputUsername">Username</label>
                      <span class="invalid-feedback"><?php echo $register['username_err']; ?></span>
                    </div>
                    <div class="form-floating mb-3">
                      <input class="form-control <?php echo (!empty($register['password_err'])) ? 'is-invalid' : ''; ?>" id="inputPassword" type="password" name="password" placeholder="Password" />
                      <label for="inputPassword">Password</label>
                      <span class="invalid-feedback"><?php echo $register['password_err']; ?></span>
                    </div>
                    <div class="form-floating mb-3">
                      <input class="form-control <?php echo (!empty($register['confirm_password_err'])) ? 'is-invalid' : ''; ?>" id="inputConfirmPassword" type="password" name="confirm_password" placeholder="Confirm Password" />
                      <label for="inputConfirmPassword">Konfirmasi Password</label>
                      <span class="invalid-feedback"><?php echo $register['confirm_password_err']; ?></span>
                    </div>
                      <button class="btn" type="submit" style="width: 370px; color: white; width: 715px; background-color: #FF9F9F">Masuk</button>
                  </form>
                </div>
                <div class="card-footer text-center py-3">
                  <div class="small"><a href="login.php">Sudah Punya Akun? Masuk!</a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <br><br><br>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js"></script>
</body>

</html>