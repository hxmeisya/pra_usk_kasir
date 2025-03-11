<?php
require 'function.php';
session_start();

$login = login($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Masuk</title>
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
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Masuk</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="form-floating mb-3">
                                        <input class="form-control <?php echo (!empty($login['username_err'])) ? 'is-invalid' : ''; ?>" id="inputUsername" type="text" name="username" placeholder="Username" />
                                        <label for="inputUsername">Username</label>
                                        <div class="invalid-feedback"><?php echo $login['username_err']; ?></div>
                                    </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control <?php echo (!empty($login['password_err'])) ? 'is-invalid' : ''; ?>" id="inputPassword" type="password" name="password" placeholder="Password" />
                                            <label for="inputPassword">Password</label>
                                            <div class="invalid-feedback"><?php echo $login['password_err']; ?></div>
                                        </div>
                                            <button class="btn" type="submit" style="width: 494px; color: white; background-color: #FF9F9F;">Masuk</button>
                                   
                                        <div class="form-check d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <div>
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" /><label class="form-check-label" for="inputRememberPassword">Ingat Saya</label>
                                            </div>
                                            <a class="small" href="password.html">Lupa Password?</a>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="register.php">Belum Punya Akun? Daftar!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>