<?php
session_start();
require_once '../function.php'; // Pastikan file function.php berisi koneksi yang benar
require_once '../vendor/setasign/fpdf/fpdf.php';

// Mencegah output sebelum PDF dibuat
ob_start();

// Pastikan user sudah melakukan transaksi
if (!isset($_GET['idtransaksi']) || $_GET['idtransaksi'] == 0) {
    die("ID Transaksi tidak valid.");
}

$idtransaksi = (int) $_GET['idtransaksi'];

// Pastikan koneksi ke database sudah ada
if (!isset($koneksi)) {
    $koneksi = mysqli_connect("localhost", "root", "", "pra_usk_kasir");

    if (!$koneksi) {
        die("Koneksi ke database gagal: " . mysqli_connect_error());
    }
}

// Ambil data transaksi
$query = "SELECT t.id, t.total_harga, t.bayar, t.kembali, t.tanggal_transaksi, u.nama 
          FROM transaksi t 
          JOIN kasir u ON t.idkasir = u.id
          WHERE t.id = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $idtransaksi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$transaksi = mysqli_fetch_assoc($result);

if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

// Ambil data detail transaksi
$query_detail = "SELECT b.nama, b.harga, k.jumlah_barang, k.subtotal
                 FROM keranjang k
                 JOIN barang b ON k.idbarang = b.id
                 WHERE k.idtransaksi = ?";

$stmt_detail = mysqli_prepare($koneksi, $query_detail);
mysqli_stmt_bind_param($stmt_detail, 'i', $idtransaksi);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);

// Buat objek PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header Struk
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Struk Pembelian', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 5, 'Maycash', 0, 1, 'C');
$pdf->Ln(5);

// Info Transaksi
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, "ID Transaksi: " . $transaksi['id'], 0, 1);
$pdf->Cell(50, 7, "Kasir: " . $transaksi['nama'], 0, 1);
$pdf->Cell(50, 7, "Tanggal: " . $transaksi['tanggal_transaksi'], 0, 1);
$pdf->Ln(5);

// Tabel Barang
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 7, 'Nama Barang', 1);
$pdf->Cell(30, 7, 'Harga', 1);
$pdf->Cell(20, 7, 'Jumlah', 1);
$pdf->Cell(30, 7, 'Subtotal', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

$total_harga = 0;

if (mysqli_num_rows($result_detail) > 0) {
    while ($detail = mysqli_fetch_assoc($result_detail)) {
        $nama_barang = $detail['nama'];
        $harga = number_format($detail['harga'], 0, ',', '.');
        $jumlah = $detail['jumlah_barang'];
        $subtotal = number_format($detail['subtotal'], 0, ',', '.');

        $pdf->Cell(80, 7, $nama_barang, 1);
        $pdf->Cell(30, 7, 'Rp ' . $harga, 1);
        $pdf->Cell(20, 7, $jumlah, 1);
        $pdf->Cell(30, 7, 'Rp ' . $subtotal, 1);
        $pdf->Ln();

        // Hitung total harga dari data transaksi
        $total_harga += $detail['subtotal'];
    }
} else {
    $pdf->Cell(160, 7, 'Tidak ada barang dalam transaksi.', 1, 1, 'C');
}
$pdf->Ln(5);

// Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 7, 'Total Belanja', 1);
$pdf->Cell(30, 7, 'Rp ' . number_format($total_harga, 0, ',', '.'), 1, 1);

$pdf->Cell(130, 7, 'Uang Dibayar', 1);
$pdf->Cell(30, 7, 'Rp ' . number_format($transaksi['bayar'], 0, ',', '.'), 1, 1);

$pdf->Cell(130, 7, 'Kembalian', 1);
$pdf->Cell(30, 7, 'Rp ' . number_format($transaksi['kembali'], 0, ',', '.'), 1, 1);

// Simpan dan Unduh PDF
$pdf_file = "struk_transaksi_" . $transaksi['id'] . ".pdf";
$pdf->Output('D', $pdf_file);
exit();
