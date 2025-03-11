<?php
require 'function.php'; // Pastikan koneksi database sudah benar

// Ambil total penjualan dan jumlah transaksi
$queryTotal = "SELECT SUM(total_harga) AS total_penjualan, COUNT(id) AS jumlah_transaksi FROM transaksi";
$resultTotal = mysqli_query($conn, $queryTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);

// Ambil produk terlaris
$queryTerlaris = "
    SELECT b.nama, SUM(dt.jumlah_barang) AS terjual 
    FROM keranjang dt 
    JOIN barang b ON dt.idbarang = b.id 
    GROUP BY dt.idbarang 
    ORDER BY terjual DESC 
    LIMIT 5";
$resultTerlaris = mysqli_query($conn, $queryTerlaris);

// Ambil stok barang rendah
$queryStok = "SELECT nama, stok FROM barang WHERE stok <= 5 ORDER BY stok ASC";
$resultStok = mysqli_query($conn, $queryStok);

// Ambil transaksi terbaru
$queryTransaksi = "
    SELECT id, idkasir, total_harga, tanggal_transaksi 
    FROM transaksi 
    ORDER BY tanggal_transaksi DESC 
    LIMIT 5";
$resultTransaksi = mysqli_query($conn, $queryTransaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MayCash</title>
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

        /* --- Dashboard Stats Full Width --- */
.dashboard-stats {
    display: flex;
    gap: 15px;
    justify-content: space-between; /* Membuat jarak rata */
    width: 100%; /* Memastikan sepanjang halaman */
}

/* --- Statistik Card --- */
.stat-card {
    flex: 1; /* Memastikan semua card memiliki ukuran yang sama */
    background: #FF9F9F; /* Peach Soft */
    color: #2C3E50;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 2px 3px 8px rgba(0, 0, 0, 0.15);
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Hover Efek */
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 3px 4px 10px rgba(0, 0, 0, 0.2);
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

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* List Stok Barang Rendah */
        .low-stock {
            list-style: none;
            padding: 0;
        }

        .low-stock li {
            background: #FDEBD0; /* Beige Muda */
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .low-stock li i {
            color: red;
            margin-right: 8px;
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
            <img src="img/avatar.jpg" alt="Avatar" class="avatar">
            <span>Meisya</span>
            <form action="../logout.php" method="post">
    <button type="submit" class="logout">Logout</button>
</form>
        </div>        
    </header>

    <aside id="sidebar">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="barang/index.php"><i class="fas fa-box"></i> Barang</a></li>
            <li><a href="transaksi/index.php"><i class="fas fa-credit-card"></i> Transaksi</a></li>
            <li><a href="riwayat/index.php"><i class="fas fa-history"></i> Riwayat</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>        
    </aside>

    <main>
        <h2>Dashboard</h2>
        <p>Ringkasan aktivitas kasir hari ini.</p>
    
        <!-- Statistik utama -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Penjualan</h3>
                <p>Rp <?= number_format($rowTotal['total_penjualan'], 0, ',', '.') ?></p>            </div>
            <div class="stat-card">
                <h3>Jumlah Transaksi</h3>
                <p><?= $rowTotal['jumlah_transaksi'] ?> Transaksi</p>
            </div>
        </div>
    
        <!-- Produk Terlaris -->
        <div class="dashboard-section">
            <h3>Produk Terlaris</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Terjual</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultTerlaris)) { ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['terjual'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    
        <!-- Stok Barang Rendah -->
        <div class="dashboard-section">
            <h3>Stok Barang Rendah</h3>
            <ul class="low-stock">
            <?php while ($row = mysqli_fetch_assoc($resultStok)) { ?>
                <li class="list-group-item"><i class="fas fa-exclamation-triangle text-warning"></i> <?= $row['nama'] ?> (<?= $row['stok'] ?>)</li>
            <?php } ?>
            </ul>
        </div>
    
        <!-- Transaksi Terbaru -->
        <div class="dashboard-section">
            <h3>Transaksi Terbaru</h3>
            <table>
                <thead>
                    <tr>
                        <th>No Transaksi</th>
                        <th>Total</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultTransaksi)) { ?>
                    <tr>
                        <td>#TRX<?= $row['id'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])) ?></td> <!-- Format tanggal dan waktu -->
                    </tr>
                <?php } ?>
                </tbody>
            </table>
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
    </script>
</body>
</html>