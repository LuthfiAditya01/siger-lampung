<?php
include '../koneksi.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

$start  = $_GET['start'];
$length = $_GET['length'];
$search = $_GET['search']['value'];
$order_col = $_GET['order'][0]['column'];
$order_dir = $_GET['order'][0]['dir'];

// Kolom sesuai urutan baru
$columns = [
    "provinsi", "kabupaten_kota", "kecamatan", "kelurahan_desa", "nomor_kartu_keluarga",
    "nomor_induk_kependudukan", "desil_nasional", "nama", "jenis_kelamin", "tanggal_lahir",
    "umur", "alamat", "jumlah_anggota_keluarga", "nama_kepala_keluarga", "partisipasi_sekolah",
    "jenjang_tertinggi_yang_diduduki", "kelas_tertinggi_yang_diduduki", "ijazah_tertinggi_yang_dimiliki",
    "nama_sekolah", "lapangan_usaha_dari_pekerjaan_utama_kepala_keluarga",
    "status_dalam_pekerjaan_utama_kepala_keluarga", "status_kepemilikan_rumah",
    "luas_bangunan_tempat_tinggal", "sumber_penerangan_utama", "daya_terpasang", "id_pelanggan_pln"
];

$order_by = $columns[$order_col];

$where = "";
if (!empty($search)) {
    $where .= "WHERE nama LIKE '%$search%' OR alamat LIKE '%$search%' OR nomor_induk_kependudukan LIKE '%$search%'";
}

$total_query = $conn->query("SELECT COUNT(*) AS total FROM dtsen");
$total = $total_query->fetch_assoc()['total'];

$filtered_query = $conn->query("SELECT COUNT(*) AS total FROM dtsen $where");
$filtered = $filtered_query->fetch_assoc()['total'];

$data_query = $conn->query("
    SELECT " . implode(", ", $columns) . "
    FROM dtsen
    $where
    ORDER BY $order_by $order_dir
    LIMIT $start, $length
");

$data = [];
while ($row = $data_query->fetch_assoc()) {
    $sub = [];
    foreach ($columns as $col) {
        $sub[] = $row[$col];
    }
    $data[] = $sub;
}

echo json_encode([
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $total,
    "recordsFiltered" => $filtered,
    "data" => $data
]);
