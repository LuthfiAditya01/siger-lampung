<?php
include '../koneksi.php';
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

// Load .env
$config = parse_ini_file(__DIR__ . '/../.env');
$encryption_key = $config['ENCRYPTION_KEY'];

// Fungsi dekripsi
function decrypt_data($encrypted_data, $key)
{
    if (!$encrypted_data) return '';
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}

// Fungsi masking untuk KK, NIK, dan alamat
function mask_data($data)
{
    if (!$data) return '';
    $first = substr($data, 0, 5);
    $starCount = strlen($data) - 5;
    if ($starCount > 10) $starCount = 10;
    return $first . str_repeat('*', $starCount);
}

// DataTables request
$start  = $_GET['start'];
$length = $_GET['length'];
$search = $_GET['search']['value'];
$order_col = $_GET['order'][0]['column'];
$order_dir = $_GET['order'][0]['dir'];

// Kolom sesuai urutan baru (pakai kolom enkripsi untuk 4 kolom ini)
$columns = [
    "provinsi",
    "kabupaten_kota",
    "kecamatan",
    "kelurahan_desa",
    "kk_enkripsi",
    "nik_enkripsi",
    "desil_enkripsi",
    "nama_enkripsi",
    "jenis_kelamin",
    "tanggal_lahir",
    "umur",
    "alamat_enkripsi",
    "jumlah_anggota_keluarga",
    "namakk_enkripsi",
    "partisipasi_sekolah",
    "jenjang_tertinggi_yang_diduduki",
    "kelas_tertinggi_yang_diduduki",
    "ijazah_tertinggi_yang_dimiliki",
    "nama_sekolah",
    "lapangan_usaha_dari_pekerjaan_utama_kepala_keluarga",
    "status_dalam_pekerjaan_utama_kepala_keluarga",
    "status_kepemilikan_rumah",
    "luas_bangunan_tempat_tinggal",
    "sumber_penerangan_utama",
    "daya_terpasang",
    "id_pelanggan_pln"
];

$order_by = $columns[$order_col];

$where = "";
if (!empty($search)) {
    // Untuk pencarian cepat, sementara hanya di kolom nama
    $where .= "WHERE nama LIKE '%$search%'";
}

$total_query = $conn->query("SELECT COUNT(*) AS total FROM dtsen");
$total = $total_query->fetch_assoc()['total'];

$filtered_query = $conn->query("SELECT COUNT(*) AS total FROM dtsen $where");
$filtered = $filtered_query->fetch_assoc()['total'];

// Ambil data
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
        if ($col === "kk_enkripsi") {
            $sub[] = mask_data(decrypt_data($row[$col], $encryption_key));
        } elseif ($col === "nik_enkripsi") {
            $sub[] = mask_data(decrypt_data($row[$col], $encryption_key));
        } elseif ($col === "alamat_enkripsi") {
            $sub[] = mask_data(decrypt_data($row[$col], $encryption_key));
        } elseif ($col === "nama_enkripsi") {
            $sub[] = decrypt_data($row[$col], $encryption_key);
        } elseif ($col === "namakk_enkripsi") {
            $sub[] = decrypt_data($row[$col], $encryption_key);
        } elseif ($col === "desil_enkripsi") {
            decrypt_data($row[$col], $encryption_key); // tetap decrypt kalau mau validasi
            $sub[] = '**';
        } else {
            $sub[] = $row[$col];
        }
    }
    $data[] = $sub;
}

echo json_encode([
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $total,
    "recordsFiltered" => $filtered,
    "data" => $data
]);
