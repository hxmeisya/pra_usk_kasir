<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID transaksi dari session
$transaksi_id = $_SESSION['idtransaksi'];

// Ambil total harga dari session
$total_harga = $_SESSION['total']; // Ubah $total menjadi $total_harga

// Ambil uang dibayar dari session
$bayar = $_SESSION['bayar'];

// Ambil kembalian dari session
$kembali = $_SESSION['kembali'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi | MayCash</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="script.js"></script>
    <title>Transaksi Berhasil</title>
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
            background: white;
            color: #4A4A4A;
            transition: background 0.3s ease-in-out;
        }

        header {
            background: #FF9F9F;
            color: #2C3E50;
            padding: 15px 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            transition: margin-left 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #2C3E50;
        }

        .user-info span {
            font-size: 16px;
            font-weight: 500;
            color: #2C3E50;
        }

        .logout {
            background: #E74C3C;
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
            background: #C0392B;
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

        aside {
            position: fixed;
            left: -250px;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #FF9F9F;
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
            background: #FDEBD0;
            padding-left: 20px;
            border-radius: 5px;
        }

        h2 {
            color: #FF9F9F;
        }

        p {
            color: #4A4A4A;
        }

        main {
            padding: 20px;
            margin-left: 0;
            transition: margin-left 0.3s;
            display: flex;
            /* Tambahkan ini untuk mengatur card ke tengah */
            justify-content: center;
            /* Tambahkan ini untuk mengatur card ke tengah */
        }

        .card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            /* Sesuaikan lebar card */
            text-align: center;
        }

        .card h2 {
            margin-bottom: 15px;
        }

        .card p {
            font-size: 1rem;
            margin: 8px 0;
        }

        .card a {
            text-decoration: none;
            background-color: #FF9F9F;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.3s, transform 0.2s;
            margin-top: 20px;
        }

        .card a:hover {
            background-color: #E68686;
        }

        .card a:active {
            transform: scale(0.95);
        }

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
        </div>
    </header>

    <aside id="sidebar">
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../barang/index.php"><i class="fas fa-box"></i> Barang</a></li>
            <li><a href="index.php"><i class="fas fa-credit-card"></i> Transaksi</a></li>
            <li><a href="../riwayat/index.php"><i class="fas fa-history"></i> Riwayat</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </aside>

    <main>
    <div class="card"> 
    <h2>Transaksi Berhasil</h2>
    <p>ID Transaksi: <?= $transaksi_id ?></p>
    <p>Total Harga: Rp <?= number_format($total_harga, 2, ',', '.') ?></p>
    <p>Uang Dibayar: Rp <?= number_format($bayar, 2, ',', '.') ?></p>
    <p>Kembalian: Rp <?= number_format($kembali, 2, ',', '.') ?></p>
    <!-- Tambahkan parameter idtransaksi ke URL -->
    <a href="struk.php?idtransaksi=<?= $transaksi_id ?>">Cetak Struk</a>
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