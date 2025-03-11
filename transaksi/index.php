<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

require '../function.php';

// Pastikan session cart ada
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Tambah ke keranjang
if (isset($_POST['tambah'])) {
    $id = intval($_POST['id']);
    $qty = intval($_POST['qty']);

    if ($id > 0 && $qty > 0) {
        $stmt = $conn->prepare("SELECT stok FROM barang WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product && $product['stok'] >= $qty) {
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;

            // Update stok di database
            $stmt = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
            $stmt->bind_param("ii", $qty, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "<script>alert('Stok tidak mencukupi untuk barang ini.');</script>";
        }
    }
}

// Ambil daftar barang dari database
$products = $conn->query("SELECT * FROM barang");
$productList = [];
while ($row = $products->fetch_assoc()) {
    $productList[$row['id']] = $row;
}

// Hitung total dari keranjang
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT harga FROM barang WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($product = $result->fetch_assoc()) {
            $total += $product['harga'] * $qty;
        }
        $stmt->close();
    }
}

// Hapus dari keranjang
if (isset($_POST['hapus'])) {
    $id = intval($_POST['id']);
    if (isset($_SESSION['cart'][$id])) {
        $qty = $_SESSION['cart'][$id];

        // Update stok di database
        $stmt = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $id);
        $stmt->execute();
        $stmt->close();

        // Hapus dari keranjang
        unset($_SESSION['cart'][$id]);
    }
}

// Proses Checkout
if (isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        if (isset($_SESSION['idkasir'])) {
            $idkasir = $_SESSION['idkasir'];
            $bayar = $_POST['uang_dibayar'];
            $total = $_POST['total'];
            $kembali = $bayar - $total;

            // Simpan transaksi ke dalam tabel transaksi
            $stmt = $conn->prepare("INSERT INTO transaksi (idkasir, tanggal_transaksi, total_harga, bayar, kembali) VALUES (?, NOW(), ?, ?, ?)");
            $stmt->bind_param("iiii", $idkasir, $total, $bayar, $kembali);
            $stmt->execute();
            $idtransaksi = $stmt->insert_id;
            $stmt->close();

            // Simpan data transaksi ke session
            $_SESSION['idtransaksi'] = $idtransaksi;
            $_SESSION['total'] = $total;
            $_SESSION['bayar'] = $bayar;
            $_SESSION['kembali'] = $kembali;

            // Simpan detail barang ke tabel keranjang
            foreach ($_SESSION['cart'] as $idbarang => $qty) {
                $harga = $productList[$idbarang]['harga'];
                $subtotal = $harga * $qty;

                $query_detail = "INSERT INTO keranjang (idtransaksi, idbarang, jumlah_barang, subtotal) 
                                 VALUES ('$idtransaksi', '$idbarang', '$qty', '$subtotal')";
                mysqli_query($conn, $query_detail);
            }

            // Kosongkan keranjang setelah transaksi selesai
            $_SESSION['cart'] = [];

            echo "<script>alert('Transaksi berhasil!'); window.location.href = 'success.php';</script>";
        } else {
            echo "<script>alert('User ID tidak ditemukan. Silakan login kembali.');</script>";
        }
    } else {
        echo "<script>alert('Keranjang kosong!');</script>";
    }
}
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

/* Style untuk input pencarian */
.search-container {
    display: flex;
    justify-content: left;
    margin-bottom: 15px;
}

.search-container input {
    width: 100%;
    max-width: 250px;
    padding: 5px;
    border: 2px solid #4A4A4A;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

.search-container input:focus {
    border-color: #FF9F9F;
    box-shadow: 0 0 8px rgba(91, 166, 107, 0.5);
}

/* Styling untuk tombol Hapus */
.btn-delete {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-delete:hover {
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

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        td input[type="number"] {
            width: 100%;
    max-width: 50px;
    padding: 5px;
    border: 2px solid #4A4A4A;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
        }

        .total-container {
    background-color: #f8f9fa; /* Warna latar belakang yang lembut */
    border: 1px solid #dee2e6; /* Garis batas */
    border-radius: 8px; /* Sudut melengkung */
    padding: 20px; /* Ruang di dalam kontainer */
    margin: 20px 0; /* Margin atas dan bawah */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan halus */
}

.total-container h3 {
    font-size: 1.5rem; /* Ukuran font untuk judul */
    margin-bottom: 15px; /* Jarak bawah judul */
    color: #343a40; /* Warna teks */
}

.total-container label {
    font-weight: bold; /* Teks label tebal */
    margin-top: 10px; /* Jarak atas label */
    display: block; /* Membuat label menjadi blok */
}

.total-container input[type="number"] {
    width: 100%; /* Lebar penuh */
    padding: 10px; /* Ruang di dalam input */
    border: 1px solid #ced4da; /* Garis batas input */
    border-radius: 4px; /* Sudut melengkung input */
    margin-top: 5px; /* Jarak atas input */
    font-size: 1rem; /* Ukuran font input */
}

.total-container .btn {
    padding: 10px 20px; /* Ruang dalam tombol */
    font-size: 1rem; /* Ukuran font tombol */
    border-radius: 4px; /* Sudut melengkung tombol */
}

.total-container .btn-primary {
    background-color: #007bff; /* Warna latar belakang tombol */
    color: white; /* Warna teks tombol */
    border: none; /* Tanpa garis batas */
}

.total-container .btn-primary:hover {
    background-color: #0056b3; /* Warna latar belakang saat hover */
    cursor: pointer; /* Menunjukkan kursor pointer saat hover */
}

.d-flex {
    display: flex; /* Menggunakan flexbox untuk tata letak */
}

.justify-content-end {
    justify-content: flex-end; /* Mengatur konten ke kanan */
}

.mt-3 {
    margin-top: 1rem; /* Margin atas */
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
        <h2>Transaksi</h2>
        
        <!-- Input pencarian (Gabungan Nama & Kode Barang) -->
        <input type="text" id="search" class="form-control mb-3" placeholder="Cari produk..." onkeyup="liveSearch()">
    
        <!-- Tabel Transaksi -->
        <table id="productTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; // Inisialisasi nomor urut
        foreach ($productList as $row): ?>
        <tr>
            <td><?= $no++ ?></td> <!-- Menampilkan nomor urut -->
            <td class="product-name"><?= $row['nama'] ?></td>
            <td>Rp <?= number_format($row['harga'], 2, ',', '.') ?></td>
            <td><?= $row['stok'] ?></td>
            <td>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="number" name="qty" placeholder="Jumlah" class="form-control" required>
                    <button type="submit" name="tambah" class="btn btn-success mt-2">Tambah ke Keranjang</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Keranjang</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total = 0; 
        $no = 1; // Tambahkan nomor urut
        foreach ($_SESSION['cart'] as $id => $qty): 
        ?>
        <tr>
            <td><?= $no++ ?></td> <!-- Menggunakan nomor urut -->
            <td><?= $productList[$id]['nama'] ?></td>
            <td><?= $qty ?></td>
            <td>Rp <?= number_format($productList[$id]['harga'] * $qty, 2, ',', '.') ?></td>
            <td>
            <form method="POST" class="d-inline">
              <input type="hidden" name="id" value="<?= $id ?>">
              <button type="submit" name="hapus" class="btn btn-danger">Batalkan</button>
          </form>
            </td>
        </tr>
        <?php 
        $total += $productList[$id]['harga'] * $qty; 
        endforeach; 
        ?>
    </tbody>
</table>

<div class="total-container">
    <h3>Total: Rp <span id="total_bayar"><?= number_format($total, 2, ',', '.') ?></span></h3>
    <form method="post" id="checkoutForm">
        <input type="hidden" name="total_bayar" value="<?= $total ?>">
        <label>Uang Dibayar:</label>
        <input type="number" name="uang_dibayar" id="uang_dibayar" class="form-control" required>
        <h3>Kembalian: Rp <span id="kembalian">0</span></h3>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" name="checkout" class="btn btn-primary">Checkout</button>
        </div>
    </form>
</div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.getElementById("hamburger");
            const body = document.body;

            hamburger.addEventListener("click", function() {
                body.classList.toggle("sidebar-open");
            });
        });

        
function liveSearch() {
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("#productTable tbody tr");
    rows.forEach(row => {
        let name = row.querySelector(".product-name").innerText.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
  }

        document.addEventListener("DOMContentLoaded", function() {
    // Fungsi liveSearch di sini
    function liveSearch() {
        let input = document.getElementById("search").value.toLowerCase();
        let rows = document.querySelectorAll("#productTable tbody tr");
        rows.forEach(row => {
            let name = row.querySelector(".product-name").innerText.toLowerCase();
            row.style.display = name.includes(input) ? "" : "none";
        });
    }

    // Tambahkan event listener untuk input pencarian
    document.getElementById("search").addEventListener("keyup", liveSearch);
});

document.getElementById("uang_dibayar").addEventListener("input", function() {
    // Mengambil total bayar dan menghapus pemisah ribuan
    let totalBayar = parseFloat(document.getElementById("total_bayar").innerText.replace(/\./g, '').replace(',', '.'));
    let uangDibayar = parseFloat(this.value) || 0; // Menggunakan parseFloat untuk menangani desimal
    let kembalian = uangDibayar - totalBayar;

    // Menampilkan kembalian dengan format yang sesuai
    document.getElementById("kembalian").innerText = kembalian >= 0 ? kembalian.toLocaleString("id-ID", { minimumFractionDigits: 0, maximumFractionDigits: 2 }) : 0;
});

function printReceipt() {
    const printWindow = window.open('', '', 'height=600,width=400');

    printWindow.document.write('<html><head><title>Struk Transaksi</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        @media print {
            body {
                font-family: "Courier New", monospace;
                font-size: 12px;
                margin: 0;
                padding: 10px;
            }
            h2, h3 {
                text-align: center;
                margin: 5px 0;
            }
            hr {
                border: none;
                border-top: 1px dashed black;
                margin: 5px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                border-bottom: 1px solid black;
                padding: 5px;
                text-align: left;
            }
            .footer {
                margin-top: 10px;
                text-align: center;
                font-size: 10px;
            }
        }
    `);
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');

    // Header Struk
    printWindow.document.write('<h2>MayCash</h2>');
    printWindow.document.write('<h3>Jl. Contoh No. 123, Jakarta</h3>');
    printWindow.document.write('<h3>Telepon: 123-456-7890</h3>');
    printWindow.document.write('<hr>');

    // Informasi Transaksi
    const tanggal = new Date().toLocaleDateString('id-ID');
    const kasir = document.querySelector('.user-info span').textContent; 

    printWindow.document.write(`<p><strong>Tanggal:</strong> ${tanggal}</p>`);
    printWindow.document.write(`<p><strong>Kasir:</strong> ${kasir}</p>`);
    printWindow.document.write('<hr>');

    // Tabel Keranjang
    printWindow.document.write('<h3>Keranjang Belanja</h3>');
    printWindow.document.write('<table>');
    printWindow.document.write(`
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
    `);

    // Ambil isi keranjang dari tabel di halaman utama
    const keranjang = document.querySelectorAll("table:nth-of-type(2) tbody tr"); 
    let no = 1;
    keranjang.forEach(row => {
        let namaBarang = row.cells[1].innerText;  // Nama Barang
        let jumlah = row.cells[2].innerText;       // Jumlah
        let subtotal = row.cells[3].innerText;     // Subtotal

        printWindow.document.write(`
            <tr>
                <td>${no++}</td>
                <td>${namaBarang}</td>
                <td>${jumlah}</td>
                <td>${subtotal}</td>
            </tr>
        `);
    });

    printWindow.document.write('</tbody></table>');
    printWindow.document.write('<hr>');

    // Total Bayar, Uang Dibayar, dan Kembalian
    const totalBayar = document.getElementById("total_bayar").innerText;
    const uangDibayar = document.getElementById("uang_dibayar").value;
    const kembalian = document.getElementById("kembalian").innerText;

    printWindow.document.write(`<p><strong>Total:</strong> Rp ${totalBayar}</p>`);
    printWindow.document.write(`<p><strong>Uang Dibayar:</strong> Rp ${uangDibayar}</p>`);
    printWindow.document.write(`<p><strong>Kembalian:</strong> Rp ${kembalian}</p>`);

    printWindow.document.write('<hr>');
    printWindow.document.write('<div class="footer">Terima kasih atas kunjungan Anda!</div>');
    printWindow.document.write('</body></html>');

    printWindow.document.close();
    printWindow.print();
}


    </script>
</body>
</html>