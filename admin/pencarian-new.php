<?php
include '../koneksi.php';
session_start();


// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login');
    exit();
}

// setelah session_start();
$a = $_SESSION['captcha_a'] ?? null;
$b = $_SESSION['captcha_b'] ?? null;

// Generate captcha HANYA jika belum ada (pertama kali buka halaman)
if ($a === null || $b === null) {
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_a'] = $a;
    $_SESSION['captcha_b'] = $b;
    $_SESSION['captcha_math'] = $a + $b;
}

// Pastikan variabel $a dan $b yang dipakai di <label> form
// SELALU diambil dari session yang sama:
$a = $_SESSION['captcha_a'];
$b = $_SESSION['captcha_b'];

$hasil_list = [];
$hasil_terpilih = null;
$pesan = '';

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Jika tombol reset ditekan
    if (isset($_POST['reset'])) {
        $hasil_list = [];
        $hasil_terpilih = null;
        $pesan = '';

        // Jika tombol pilih ditekan (step 2, tidak perlu verifikasi captcha)
    } elseif (isset($_POST['pilih_nik'])) {
        $nik_pilih = $_POST['pilih_nik'];

        // Gunakan hash untuk cari NIK
        $nik_hash = hash('sha256', $nik_pilih);
        $stmt = $conn->prepare("SELECT * FROM dtsen WHERE nik_hash = ? LIMIT 1");
        $stmt->bind_param("s", $nik_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasil_terpilih = $result->fetch_assoc();

        if ($hasil_terpilih) {
            // Dekripsi data terenkripsi
            $hasil_terpilih['nomor_kartu_keluarga'] = decrypt_data($hasil_terpilih['kk_enkripsi'], $encryption_key);
            $hasil_terpilih['nomor_induk_kependudukan'] = decrypt_data($hasil_terpilih['nik_enkripsi'], $encryption_key);
            $hasil_terpilih['alamat'] = decrypt_data($hasil_terpilih['alamat_enkripsi'], $encryption_key);
            $hasil_terpilih['desil_nasional'] = decrypt_data($hasil_terpilih['desil_enkripsi'], $encryption_key);

            $nama_kelurahan = $hasil_terpilih['kelurahan_desa'] ?? '';
            $kode_kelurahan = '';

            if ($nama_kelurahan) {
                $stmt_kode = $conn->prepare("SELECT kode_kelurahan FROM wilayah_kelurahan WHERE nama_kelurahan = ? LIMIT 1");
                $stmt_kode->bind_param("s", $nama_kelurahan);
                $stmt_kode->execute();
                $stmt_kode->bind_result($kode_kelurahan);
                $stmt_kode->fetch();
                $stmt_kode->close();

                if ($kode_kelurahan) {
                    $sql_wil = "
                        SELECT kp.nama_provinsi, kb.nama_kabupaten, kc.nama_kecamatan, kl.nama_kelurahan 
                        FROM wilayah_kelurahan kl
                        JOIN wilayah_kecamatan kc ON kl.kode_kecamatan = kc.kode_kecamatan
                        JOIN wilayah_kabupaten kb ON kc.kode_kabupaten = kb.kode_kabupaten
                        JOIN wilayah_provinsi kp ON kb.kode_provinsi = kp.kode_provinsi
                        WHERE kl.kode_kelurahan = ?
                        LIMIT 1
                    ";
                    $stmt = $conn->prepare($sql_wil);
                    $stmt->bind_param("s", $kode_kelurahan);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $wilayah = $result->fetch_assoc();
                }
            }
        } else {
            $pesan = "Data yang dipilih tidak ditemukan.";
        }

        // Jika melakukan pencarian (step 1, WAJIB verifikasi captcha)
    } else {
        // Verifikasi captcha hanya untuk pencarian
        if (!isset($_POST['captcha']) || !isset($_SESSION['captcha_math'])) {
            $pesan = "Sesi verifikasi kedaluwarsa. Muat ulang halaman.";
        } elseif ((int)$_POST['captcha'] !== (int)$_SESSION['captcha_math']) {
            $pesan = "Verifikasi salah, silakan coba lagi.";

            // JANGAN regenerate di sini, biarkan $a,$b tetap sama agar user bisa coba lagi
            // (form akan menampilkan $a dan $b dari session yang sama)
        } else {
            $kode_kelurahan = $_POST['kelurahan'] ?? '';
            $keyword = trim($_POST['keyword'] ?? '');

            if (!$kode_kelurahan || !$keyword) {
                $pesan = "Mohon lengkapi semua kolom pencarian.";
            } else {
                $stmt_kel = $conn->prepare("SELECT nama_kelurahan FROM wilayah_kelurahan WHERE kode_kelurahan = ?");
                $stmt_kel->bind_param("s", $kode_kelurahan);
                $stmt_kel->execute();
                $stmt_kel->bind_result($nama_kelurahan);
                $stmt_kel->fetch();
                $stmt_kel->close();

                if (!$nama_kelurahan) {
                    $pesan = "Kelurahan tidak ditemukan.";
                } else {
                    // Cek hash NIK dan KK untuk pencarian cepat
                    // $kk_hash = hash('sha256', $keyword);
                    // $nik_hash = hash('sha256', $keyword);

                    // $sql = "SELECT * FROM dtsen 
                    //     WHERE kelurahan_desa = ?
                    //     AND (
                    //         kk_hash = ? OR 
                    //         nik_hash = ? OR 
                    //         nama LIKE ? OR 
                    //         nama_kepala_keluarga LIKE ?
                    //     )";
                    // $stmt = $conn->prepare($sql);
                    // $like = '%' . $keyword . '%';
                    // $stmt->bind_param("sssss", $nama_kelurahan, $kk_hash, $nik_hash, $like, $like);
                    // $stmt->execute();
                    // $result = $stmt->get_result();
                    // $hasil_list = $result->fetch_all(MYSQLI_ASSOC);

                    // // Dekripsi data sebelum ditampilkan
                    // foreach ($hasil_list as &$item) {
                    //     $item['nomor_kartu_keluarga'] = decrypt_data($item['kk_enkripsi'], $encryption_key);
                    //     $item['nomor_induk_kependudukan'] = decrypt_data($item['nik_enkripsi'], $encryption_key);
                    //     $item['alamat'] = decrypt_data($item['alamat_enkripsi'], $encryption_key);
                    //     $item['desil_nasional'] = decrypt_data($item['desil_enkripsi'], $encryption_key);
                    // }

                    // if (empty($hasil_list)) {
                    //     $pesan = "Data tidak ditemukan.";
                    // }
                    $kk_hash = hash('sha256', $keyword);
                    $nik_hash = hash('sha256', $keyword);
                    $nama_hash = hash('sha256', $keyword);
                    $namakk_hash = hash('sha256', $keyword);

                    $sql = "SELECT * FROM dtsen 
                        WHERE kelurahan_desa = ?
                        AND (
                            kk_hash = ? OR 
                            nik_hash = ? OR 
                            nama_hash = ? OR
                            namakk_hash = ?
                        )";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $nama_kelurahan, $kk_hash, $nik_hash, $nama_hash, $namakk_hash);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $hasil_list = $result->fetch_all(MYSQLI_ASSOC);

                    // Dekripsi semua field yang perlu
                    foreach ($hasil_list as &$item) {
                        $item['nama'] = decrypt_data($item['nama_enkripsi'], $encryption_key);
                        $item['nama_kepala_keluarga'] = decrypt_data($item['namakk_enkripsi'], $encryption_key);
                        $item['nomor_kartu_keluarga'] = decrypt_data($item['kk_enkripsi'], $encryption_key);
                        $item['nomor_induk_kependudukan'] = decrypt_data($item['nik_enkripsi'], $encryption_key);
                        $item['alamat'] = decrypt_data($item['alamat_enkripsi'], $encryption_key);
                        $item['desil_nasional'] = decrypt_data($item['desil_enkripsi'], $encryption_key);
                    }

                    // Kalau tidak ketemu via hash, lakukan LIKE manual
                    if (empty($hasil_list)) {
                        $sql2 = "SELECT * FROM dtsen WHERE kelurahan_desa = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("s", $nama_kelurahan);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $all_data = $result2->fetch_all(MYSQLI_ASSOC);

                        $filtered = [];
                        foreach ($all_data as &$row) {
                            $row['nama'] = decrypt_data($row['nama_enkripsi'], $encryption_key);
                            $row['nama_kepala_keluarga'] = decrypt_data($row['namakk_enkripsi'], $encryption_key);

                            if (
                                stripos($row['nama'], $keyword) !== false ||
                                stripos($row['nama_kepala_keluarga'], $keyword) !== false
                            ) {
                                // tambahkan juga dekripsi lengkap kalau perlu
                                $row['nomor_kartu_keluarga'] = decrypt_data($row['kk_enkripsi'], $encryption_key);
                                $row['nomor_induk_kependudukan'] = decrypt_data($row['nik_enkripsi'], $encryption_key);
                                $row['alamat'] = decrypt_data($row['alamat_enkripsi'], $encryption_key);
                                $row['desil_nasional'] = decrypt_data($row['desil_enkripsi'], $encryption_key);

                                $filtered[] = $row;
                            }
                        }

                        $hasil_list = $filtered;
                    }

                    if (empty($hasil_list)) {
                        $pesan = "Data tidak ditemukan.";
                    }
                }
            }

            // === Regenerate captcha untuk submit berikutnya (HANYA setelah verifikasi sukses) ===
            $a = random_int(1, 9);
            $b = random_int(1, 9);
            $_SESSION['captcha_a'] = $a;
            $_SESSION['captcha_b'] = $b;
            $_SESSION['captcha_math'] = $a + $b;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pencarian - Siger Bandar Lampung</title>
    <link rel="stylesheet" href="../css/output.css" />
    <link rel="shortcut icon" href="../img/logo.jpg" type="image/x-icon">
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Add Heroicons via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">
    <style>
        /* Gradient button with smooth transition */
        .gradient-btn {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .gradient-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, #0891b2, #2563eb);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        .gradient-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom left, #0891b2, #2563eb);
            z-index: -2;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gradient-btn:hover::before {
            opacity: 0;
        }

        .gradient-btn:hover::after {
            opacity: 1;
        }

        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-col {
            flex: 1;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-select,
        .form-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .form-button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cari {
            background-color: #2563eb;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-cari:hover {
            background-color: #1e40af;
        }

        .btn-reset {
            background-color: #94a3b8;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-reset:hover {
            background-color: #64748b;
        }

        th,
        td {
            text-align: left !important;
            vertical-align: middle !important;
            padding-right: 50px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include '../components/aside.php'; ?>
        <!-- Main Content -->
        <main id="mainContent" class="flex-1 overflow-x-hidden overflow-y-auto transition-all duration-300 ease-in-out">
            <div class="p-6">
                <!-- Header with Toggle Button -->
                <div class="flex items-center mb-6">
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-2xl font-semibold ml-4">Pencarian</h1>
                </div>

                <!-- Content -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Halaman Pencarian</h2>
                </div>

                <!-- Pencarian -->
                <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
                    <!-- Form -->
                    <form method="post" class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">

                        <!-- 2 kolom wilayah -->
                        <div class="form-row">
                            <div class="form-col">
                                <label>Provinsi<span style="color:red !important">*</span></label>
                                <select name="provinsi" id="provinsi" class="form-select" required>
                                    <option value="">Pilih Provinsi</option>
                                    <?php
                                    $q = $conn->query("SELECT * FROM wilayah_provinsi ORDER BY nama_provinsi");
                                    while ($row = $q->fetch_assoc()) {
                                        echo "<option value='{$row['kode_provinsi']}'>{$row['nama_provinsi']}</option>";
                                    }
                                    ?>
                                </select>

                                <label>Kecamatan<span style="color:red !important">*</span></label>
                                <select name="kecamatan" id="kecamatan" class="form-select" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>

                            </div>

                            <div class="form-col">
                                <label>Kabupaten/Kota<span style="color:red !important">*</span></label>
                                <select name="kabupaten" id="kabupaten" class="form-select" required>
                                    <option value="">Pilih Kabupaten</option>
                                </select>

                                <label>Kelurahan<span style="color:red !important">*</span></label>
                                <select name="kelurahan" id="kelurahan" class="form-select" required>
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input Keyword -->
                        <div class="form-group">
                            <label>Nomor KK / NIK / Nama<span style="color:red !important">*</span></label>
                            <input type="text" name="keyword" class="form-input" placeholder="Masukkan kata kunci" required>
                        </div>

                        <!-- Captcha hitungan -->
                        <div class="form-group">
                            <label>Hitung: <?= $a ?> + <?= $b ?> = ?<span style="color:red !important">*</span></label>
                            <input type="number" name="captcha" required placeholder="Jawaban"
                                class="form-input block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Tombol -->
                        <div class="form-button-group">
                            <button type="submit" class="btn-cari">Cari</button>
                            <button type="submit" name="reset" class="btn-reset">Reset</button>
                        </div>
                    </form>



                    <!-- Hasil Terpilih -->
                    <?php if ($hasil_terpilih): ?>
                        <div class="max-w-6xl mx-auto my-8 px-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <br>
                                <!-- Kolom Wilayah -->
                                <div class="bg-white shadow rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 px-4 py-2 font-semibold text-gray-700 border-b">Identitas Wilayah</div>
                                    <table class="w-full text-sm text-left text-gray-700">
                                        <tbody>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 w-1/3 font-medium bg-gray-50">Provinsi</th>
                                                <td><?= htmlspecialchars($wilayah['nama_provinsi'] ?? '-') ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">Kabupaten/Kota</th>
                                                <td><?= htmlspecialchars($wilayah['nama_kabupaten'] ?? '-') ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">Kecamatan</th>
                                                <td><?= htmlspecialchars($wilayah['nama_kecamatan'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 font-medium bg-gray-50">Kelurahan</th>
                                                <td><?= htmlspecialchars($wilayah['nama_kelurahan'] ?? '-') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <!-- Kolom Data Individu -->
                                <div class="bg-white shadow rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 px-4 py-2 font-semibold text-gray-700 border-b"> Identitas Individu</div>
                                    <table class="w-full text-sm text-left text-gray-700">
                                        <tbody>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 w-1/3 font-medium bg-gray-50">Nama</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['nama']) ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">NIK</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['nomor_induk_kependudukan']) ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">Umur</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['umur'] . " tahun") ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">No KK</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['nomor_kartu_keluarga']) ?></td>
                                            </tr>
                                            <tr class="border-b">
                                                <th class="px-4 py-2 font-medium bg-gray-50">Nama Kepala Keluarga</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['nama_kepala_keluarga']) ?></td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 font-medium bg-gray-50">Desil</th>
                                                <td class="px-4 py-2"><?= htmlspecialchars($hasil_terpilih['desil_nasional']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if (!empty($hasil_list)): ?>
                        <form method="post" class="mt-6">
                            <div class="overflow-x-auto">
                                <!-- <table class="w-full text-sm border border-gray-300 bg-white"> -->
                                <table class="w-full min-w-max text-sm border border-gray-300 bg-white">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border p-2">No</th>
                                            <th class="border p-2">NIK</th>
                                            <th class="border p-2">No KK</th>
                                            <th class="border p-2">Nama</th>
                                            <th class="border p-2">Kepala Keluarga</th>
                                            <th class="border p-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody> <?php foreach ($hasil_list as $i => $row): ?> <tr>
                                                <td class="border p-2 text-center"><?= $i + 1 ?></td>
                                                <td class="border p-2"><?= htmlspecialchars($row['nomor_induk_kependudukan']) ?></td>
                                                <td class="border p-2"><?= htmlspecialchars($row['nomor_kartu_keluarga']) ?></td>
                                                <td class="border p-2"><?= htmlspecialchars($row['nama']) ?></td>
                                                <td class="border p-2"><?= htmlspecialchars($row['nama_kepala_keluarga']) ?></td>
                                                <td class="border p-2 text-center"> <button type="submit" name="pilih_nik" value="<?= htmlspecialchars($row['nomor_induk_kependudukan']) ?>" class="btn-cari">Pilih</button> </td>
                                            </tr> <?php endforeach; ?>
                                    </tbody>

                                </table>
                            </div>
                        </form>
                    <?php endif; ?>

                    <?php if ($pesan): ?>
                        <div class="mt-4 bg-red-100 text-red-800 p-3 rounded"><?= $pesan ?></div>
                    <?php endif; ?>

                    <?php if (!empty($hasil_list)): ?>
                        <!-- tampilkan tabel hasil -->
                    <?php endif; ?>

                    <?php if (!empty($hasil_terpilih)): ?>
                        <!-- tampilkan detail hasil pilihan -->
                    <?php endif; ?>

                    <?php if (!empty($pesan)): ?>
                        <!-- <div class="notif-error"><?= $pesan ?></div> -->
                    <?php endif; ?>



                </div>

                <!-- AJAX -->
                <script>
                    $(document).ready(function() {
                        $('#provinsi').on('change', function() {
                            const id = $(this).val();
                            $('#kabupaten').html('<option>Loading...</option>');
                            $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
                            $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');
                            if (id) {
                                $.get('get_wilayah.php', {
                                    tipe: 'kabupaten',
                                    id: id
                                }, function(data) {
                                    let options = '<option value="">-- Pilih Kabupaten --</option>';
                                    data.forEach(function(item) {
                                        options += `<option value="${item.kode_kabupaten}">${item.nama_kabupaten}</option>`;
                                    });
                                    $('#kabupaten').html(options);
                                }, 'json');
                            }
                        });

                        $('#kabupaten').on('change', function() {
                            const id = $(this).val();
                            $('#kecamatan').html('<option>Loading...</option>');
                            $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');
                            if (id) {
                                $.get('get_wilayah.php', {
                                    tipe: 'kecamatan',
                                    id: id
                                }, function(data) {
                                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                                    data.forEach(function(item) {
                                        options += `<option value="${item.kode_kecamatan}">${item.nama_kecamatan}</option>`;
                                    });
                                    $('#kecamatan').html(options);
                                }, 'json');
                            }
                        });

                        $('#kecamatan').on('change', function() {
                            const id = $(this).val();
                            $('#kelurahan').html('<option>Loading...</option>');
                            if (id) {
                                $.get('get_wilayah.php', {
                                    tipe: 'kelurahan',
                                    id: id
                                }, function(data) {
                                    let options = '<option value="">-- Pilih Kelurahan --</option>';
                                    data.forEach(function(item) {
                                        options += `<option value="${item.kode_kelurahan}">${item.nama_kelurahan}</option>`;
                                    });
                                    $('#kelurahan').html(options);
                                }, 'json');
                            }
                        });

                        // Tombol Pilih dari Modal
                        $(document).on('click', '.pilih-btn', function() {
                            const nik = $(this).data('nik');
                            const kk = $(this).data('kk');
                            const nama = $(this).data('nama');
                            const kepala = $(this).data('kepala');

                            $('#vnik').text(nik);
                            $('#vkk').text(kk);
                            $('#vnama').text(nama);
                            $('#vkepala').text(kepala);

                            $('#hasilTerpilih').removeClass('hidden');
                            $('#modalPilih').hide();
                        });
                    });
                </script>

            </div>
        </main>
    </div>
</body>

</html>