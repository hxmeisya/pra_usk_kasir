<?php
//koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "pra_usk_kasir");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data)
{
    global $conn;
    $gambar = htmlspecialchars($data["gambar"]);
    $nama = htmlspecialchars($data["nama"]);
    $kode_barang = htmlspecialchars($data["kode_barang"]);
    $harga = htmlspecialchars($data["harga"]);
    $stok = htmlspecialchars($data["stok"]);

    // upload gambar
    $gambar = upload();
    if (!$gambar) {

        return false;
    }

    $query = "INSERT INTO barang
                VALUES
                ('', '$gambar', '$nama', '$kode_barang', '$harga', '$stok' );
                ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function upload()
{
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    //cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        echo "<script>
               alert('pilih gambar terlebih dahulu!');
               </script>";
        return false;
    }

    //cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>
               alert('yang anda upload bukan gambar!');
               </script>";

        return false;
    }

    //cek jika ukurannya terlalu besar
    if ($ukuranFile > 1000000) {
        echo "<script>
               alert('ukuran gambar terlalu besar!');
               </script>";

        return false;
    }

    //lolos pengecekan,  gambar siap diupload
    //generate nama gambar baru
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    move_uploaded_file($tmpName, '../img/' . $namaFileBaru);

    return $namaFileBaru;
}

function edit($data)
{
    global $conn;
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $kode_barang = htmlspecialchars($data["kode_barang"]);
    $harga = htmlspecialchars($data["harga"]);
    $stok = htmlspecialchars($data["stok"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);

    //cek apakah user pilih gambar baru atau tidak
    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }

    $query = "UPDATE barang SET
                nama = '$nama',
                kode_barang = '$kode_barang',
                harga = '$harga',
                stok = '$stok',
                gambar = '$gambar'
                WHERE id = $id
                ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function hapus($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM barang WHERE id = $id");

    return mysqli_affected_rows($conn);
}

function input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function register($conn)
{
    $nama = $username = $password = $confirm_password = "";
    $nama_err = $username_err = $password_err = $confirm_password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validasi nama
        if (empty(input($_POST["nama"]))) {
            $nama_err = "Please enter a name.";
        } else {
            $nama = input($_POST["nama"]);
        }

        // Validasi username
        if (empty(input($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            $username = input($_POST["username"]);
            // Cek jika username sudah digunakan di database
            $sql = "SELECT id FROM kasir WHERE username = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $username_err = "This username is already taken.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }

        // Validasi password
        if (empty(input($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(input($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = input($_POST["password"]);
        }

        // Validasi confirm password
        if (empty(input($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";
        } else {
            $confirm_password = input($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }

        if (empty($nama_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
            $sql = "INSERT INTO kasir (nama, username, password) VALUES (?, ?, ?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $nama, $username, $param_password);
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

                if (mysqli_stmt_execute($stmt)) {
                    echo
                    "<script>
                        alert('Registrasi berhasil, silakan login');
                        window.location.href = 'login.php';
                    </script>";
                } else {
                    echo "<script>alert('Oops, tampaknya ada yang salah, tolong coba lagi!')</script>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    return [
        'nama' => $nama,
        'username' => $username,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'nama_err' => $nama_err,
        'username_err' => $username_err,
        'password_err' => $password_err,
        'confirm_password_err' => $confirm_password_err
    ];
}

function login($conn)
{
    $username = $password = "";
    $username_err = $password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST["username"]))) {
            $username_err = "Tolong isi username.";
        } else {
            $username = trim($_POST["username"]);
        }

        if (empty(trim($_POST["password"]))) {
            $password_err = "Tolong isi password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty($username_err) && empty($password_err)) {
            $sql = "SELECT id, nama, username, password FROM kasir WHERE username = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $nama, $username, $hashed_password);
                        if (mysqli_stmt_fetch($stmt)) {
                            if (password_verify($password, $hashed_password)) {
                                // Set session variables
                                session_start();
                                $_SESSION["login"] = true;
                                $_SESSION["idkasir"] = $id;
                                $_SESSION["nama"] = $nama;
                                setcookie("nama", $nama, time() + (86400 * 2), "/"); // Cookie berlaku selama 2 hari

                                // Redirect ke halaman dashboard setelah login
                                header("location: index.php");
                                exit();
                            } else {
                                $password_err = "Password yang kamu isi tidak valid.";
                            }
                        }
                    } else {
                        $username_err = "Tidak menemukan akun dengan username $username.";
                    }
                } else {
                    echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    return [
        'username' => $username,
        'password' => $password,
        'username_err' => $username_err,
        'password_err' => $password_err
    ];
}
