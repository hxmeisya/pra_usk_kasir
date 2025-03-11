<?php
session_start();

require '../function.php';
if (isset($_POST["submit"])) {

    if (tambah($_POST) > 0) {
        echo "
        <script>
        alert('data berhasil ditambahkan!');
        document.location.href = '../barang/index.php';
        </script>
        ";
    } else {
        echo "
        <script>
        alert('data gagal ditambahkan!');
        document.location.href = '../barang/index.php';
        </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | MayCash</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="script.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: white; /* Navy Blue */
            color: #4A4A4A; /* Dark Grey */
            transition: background 0.3s ease-in-out;
        }

        /* Header */
        header {
            background: #FF9F9F; /* Peach Soft */
            color: #2C3E50;
            padding: 15px 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            transition: margin-left 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            gap: 10px;
        }

        /* User Info */
        .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px; /* Jarak antar elemen */
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%; /* Agar bentuknya bulat */
            object-fit: cover; /* Agar gambar tetap proporsional */
            border: 2px solid #2C3E50; /* Bingkai kecil */
        }

        /* Nama Kasir */
        .user-info span {
            font-size: 16px;
            font-weight: 500;
            color: #2C3E50;
        }

        /* Logout Button */
        .logout {
            background: #E74C3C; /* Warna merah */
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .logout:hover {
            background: #C0392B; /* Merah lebih gelap saat hover */
        }

        .logout:active {
            transform: scale(0.95);
        }

        .hamburger {
            font-size: 24px;
            background: none;
            border: none;
            color: #2C3E50;
            cursor: pointer;
            margin-right: 15px;
            transition: transform 0.2s ease;
        }

        .hamburger:hover {
            transform: scale(1.1);
        }

        button a {
    text-decoration: none; /* Menghilangkan garis bawah */
    color: white; /* Warna tulisan putih */
    display: inline-block; /* Memastikan elemen <a> menyesuaikan ukuran tombol */
    width: 100%; /* Agar link memenuhi tombol */
    height: 100%;
    text-align: center;
    padding: 5px 0;
}

.btn-back {
    text-decoration: none;
    background-color: #FF9F9F;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 20px;
    font-weight: bold;
    transition: background 0.3s ease;
}

.btn-back:hover {
    background-color: #E68686; /* Warna hover */
}

        /* Sidebar */
        aside {
            position: fixed;
            left: -250px;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #FF9F9F; /* Peach Soft */
            padding-top: 60px;
            transition: 0.3s;
            box-shadow: 4px 0px 10px rgba(0, 0, 0, 0.3);
        }

        aside ul {
            list-style: none;
        }

        aside ul li {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        aside ul li a {
            color: #2C3E50;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 600;
            transition: background 0.3s, padding-left 0.3s;
            padding: 10px 15px;
            border-radius: 5px;
        }

        aside ul li a i {
            margin-right: 10px;
        }

        aside ul li a:hover {
            background: #FDEBD0; /* Beige Muda */
            padding-left: 20px;
            border-radius: 5px;
        }

        /* Main Content */
        main {
            padding: 20px;
            margin-left: 0;
            transition: margin-left 0.3s;
        }

        h1 {
    margin: 0; /* Menghilangkan margin default supaya sejajar */
    font-size: 24px;
}

        h2 {
            color: #FF9F9F; /* Peach Soft */
        }

        p {
            color: #4A4A4A;
        }
        .content {
        width: 80%;
        max-width: 600px;
        margin: 20px auto;
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Styling for the form */
    form {
        display: flex;
        flex-direction: column;
    }

    form ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    form li {
        margin-bottom: 15px;
    }

    form label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    form input{
        width: 95%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 10px;
    }

    form img {
        border-radius: 5px;
    }

    form button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        background-color: #FF9F9F;
        color: white;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: #E68686 ;
    }

        /* Sidebar Open */
        .sidebar-open aside {
            left: 0;
        }

        .sidebar-open main {
            margin-left: 250px;
        }

        .sidebar-open header {
            margin-left: 250px;
        }
    </style>
</head>
<body>
    <header>
        <button class="hamburger" id="hamburger">
            ☰
        </button>
        <h1>MayCash</h1>
        <div class="user-info">
            <img src="../img/avatar.jpg" alt="Avatar" class="avatar">
            <span>Meisya</span>
            <form action="../logout.php" method="post">
                <button type="submit" class="logout">Logout</button>
            </form>
                    </div>        
    </header>

    <aside id="sidebar">
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="index.php"><i class="fas fa-box"></i> Barang</a></li>
            <li><a href="../transaksi/index.php"><i class="fas fa-credit-card"></i> Transaksi</a></li>
            <li><a href="../riwayat/index.php"><i class="fas fa-history"></i> Riwayat</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>        
    </aside>

    <main class="container">
    <div class="header">
    <a href="index.php" class="btn-back">←</a>
    <h1>Tambah Barang</h1>
</div>

    <div class="content">
        <form action="" method="post" enctype="multipart/form-data">
            <ul>
                <li>
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" id="nama" required>
                </li>
                <li>
                    <label for="kode">Kode Barang</label>
                    <input type="text" name="kode_barang" id="kode_barang" required>
                </li>
                <li>
                    <label for="harga">Harga</label>
                    <input type="text" name="harga" id="harga" required>
                </li>
                <li>
                    <label for="stok">Stok</label>
                    <input type="number" name="stok" id="stok" required>
                </li>
                <li>
                    <label for="gambar">Gambar</label>
                    <input type="file" name="gambar" id="gambar">
                </li>
                <li>
                    <button type="submit" name="submit">Tambah</button>
                </li>
            </ul>
        </form>
    </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.getElementById("hamburger");
            const body = document.body;

            hamburger.addEventListener("click", function() {
                body.classList.toggle("sidebar-open");
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', function () {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('#barangTable tbody tr');
        rows.forEach(row => {
            let itemName = row.cells[2].textContent.toLowerCase();
            row.style.display = itemName.includes(searchValue) ? '' : 'none';
        });
    });

    function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("barangTable");
    switching = true;
    dir = "asc"; 

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount === 0 && dir === "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
    </script>
</body>
</html>