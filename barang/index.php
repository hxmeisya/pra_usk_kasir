<?php

session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

require '../function.php';

$barang = query("SELECT * FROM barang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang | MayCash</title>
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

/* Styling untuk tombol Tambah Barang */
.btn-success {
    display: inline-block;
    background-color: #5ba66b; /* Warna hijau sesuai proyek */
    color: white;
    font-size: 16px;
    font-weight: 600;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.3s, transform 0.2s;
    text-align: center;
}

.btn-success:hover {
    background-color: #4a8b58; /* Warna hijau lebih gelap saat hover */
}

.btn-success:active {
    transform: scale(0.95);
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

        h2 {
            color: #FF9F9F; /* Peach Soft */
        }

        p {
            color: #4A4A4A;
        }

        /* Styling untuk input pencarian */
#searchInput {
    width: 100%;
    max-width: 200px;
    padding: 5px;
    border: 2px solid #4A4A4A;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

#searchInput:focus {
    border-color: #FF9F9F;
    box-shadow: 0 0 8px rgba(91, 166, 107, 0.5);
}

.toolbar {
    display: flex;
    justify-content: space-between; /* Membuat elemen di kiri dan kanan */
    align-items: center; /* Agar tetap sejajar */
    padding: 10px;
    margin-bottom: 15px;
}

/* Styling untuk tombol Edit */
.btn-warning {
    background-color: #f39c12;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-warning:hover {
    background-color: #d68910;
}

/* Styling untuk tombol Hapus */
.btn-danger {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-danger:hover {
    background-color: #c0392b;
}

        /* Section Dashboard */
        .dashboard-section {
            margin-top: 20px;
        }

        .dashboard-section h3 {
            color: #FF9F9F;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #FF9F9F;
            color: white;
        }

        th {
    cursor: pointer;
}

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
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
        <h2>Daftar Barang</h2>
        <div class="container">
            <div class="toolbar">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari barang...">
                <a href="tambah.php" class="btn btn-success">+ Tambah Barang</a>
            </div>
        </div>        
    
        <!-- Tabel barang -->
        <table id="barangTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th onclick="sortTable(2)">Nama Barang &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(3)">Kode Barang &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(4)">Harga &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(5)">Stok &#x25B2;&#x25BC;</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach( $barang as $row ) : ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><img src="../img/<?= $row["gambar"]; ?>" width="80"></td>
                        <td><?= $row["nama"]; ?></td>
                        <td><?= $row["kode_barang"]; ?></td>
                        <td><?= $row["harga"]; ?></td>
                        <td><?= $row["stok"]; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm">
                                <a href="edit.php?id=<?= $row["id"]; ?>" class="text-white">Edit</a>
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <a href="hapus.php?id=<?= $row["id"]; ?>" class="text-white">Hapus</a>
                            </button>                            
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
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